//
//  InfoWindowTableViewController.swift
//  P4P
//
//  Created by Daniel Yang on 5/1/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//
//  view controller for the information window that appears when you tap on a
//  marker on the Google Maps view. shows the person that you tapped on, what open 
//  offers they have posted, and allows you to pursue that offer (ie, make requests)
//

import UIKit
import SwiftyJSON

class InfoWindowTableViewController: UITableViewController {

    // global variables
    // information passed from GoogleMapsViewController
    var mapInfoWindowNetID: String = ""
    var mapInfoWindowName: String = ""
    var mapInfoWindowNumberOffers: String = ""
    var mapInfoExchangeArray: [String] = []
    var mapInfoExchangeIDArray: [String] = []
    
    // information recovered from global app delegate
    var appNetID = ""
    var websiteURLbase = ""
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
        
        // pull the base for website URL for making http requests and the current logged in user netID
        let appDelegate = UIApplication.sharedApplication().delegate as! AppDelegate
        appNetID = appDelegate.userNetid
        websiteURLbase = appDelegate.websiteURLBase
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }

    // MARK: - Table view data source
    
    override func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        // Return the number of sections.
        return 1
    }
    
    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // Return the number of rows in the section.
        return (mapInfoExchangeArray.count)
    }
    
    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        // create a generic cell and set the text as the exchange details: "ClubName (NumberPasses)"
        let cell = tableView.dequeueReusableCellWithIdentifier("MarkerOfferCells", forIndexPath: indexPath) as! UITableViewCell
        cell.textLabel!.text = mapInfoExchangeArray[indexPath.row]
        
        // need to check if the offer contains the current logged in user
        var offerID = mapInfoExchangeIDArray[indexPath.row]
        var getExchangeWithID = self.websiteURLbase + "/php/getExchangeById.php?exchangeId=" + offerID

        let url = NSURL(string: getExchangeWithID)
        
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            
            // obtain list of netIDs of people who have made a request for an exchange
            var arrayExchangeString = (json[0]["associatedExchanges"].string)
            
            if let dataFromString = arrayExchangeString!.dataUsingEncoding(NSUTF8StringEncoding, allowLossyConversion: false) {
                let jsonExchange = JSON(data: dataFromString)
                
                var index = 0
                
                // check every netID to see if matches current user netID
                for (key: String, subJson: JSON) in jsonExchange {
                    if (jsonExchange[index].string == self.appNetID) {
                        // if so, make the current cell unselectable and display a checkmark to indicate
                        dispatch_async(dispatch_get_main_queue()) {
                            cell.accessoryType = UITableViewCellAccessoryType.Checkmark
                            cell.selectionStyle = UITableViewCellSelectionStyle.None
                            cell.setNeedsDisplay()
                        }
                    }
                    index++
                }
            }
            

        }

        task.resume()
        return cell
    }
    
    // if you select a cell, make the request and change how the cell is displayed
    override func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath) {
        let cell = tableView.cellForRowAtIndexPath(indexPath)
        if cell!.accessoryType != UITableViewCellAccessoryType.Checkmark
        {
            // make the current cell unselectable and display a checkmark to indicate
            cell!.accessoryType = UITableViewCellAccessoryType.Checkmark
            cell!.selectionStyle = UITableViewCellSelectionStyle.None

            // generate URL to http request pursuing an offer
            var pursueOfferString = self.websiteURLbase + "/php/pursueOffer.php?"
            pursueOfferString += "netId=" + appNetID + "&offerId=" + mapInfoExchangeIDArray[indexPath.row]
            
            // make the request
            let url = NSURL(string: pursueOfferString)
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                dispatch_async(dispatch_get_main_queue()) {
                    
                }
            }
            task.resume()
        }
    }

}
