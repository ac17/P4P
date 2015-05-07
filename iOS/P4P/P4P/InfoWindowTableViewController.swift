//
//  InfoWindowTableViewController.swift
//  P4P
//
//  Created by Daniel Yang on 5/1/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit
import SwiftyJSON

class InfoWindowTableViewController: UITableViewController {

    var mapInfoWindowNetID: String = ""
    var mapInfoWindowName: String = ""
    var mapInfoWindowNumberOffers: String = ""
    var mapInfoExchangeArray: [String] = []
    var mapInfoExchangeIDArray: [String] = []
    var appNetID = ""
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
        
        let appDelegate = UIApplication.sharedApplication().delegate as! AppDelegate
        appNetID = appDelegate.userNetid
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }

    // MARK: - Table view data source
    
    override func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        // #warning Potentially incomplete method implementation.
        // Return the number of sections.
        return 1
    }
    
    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete method implementation.
        // Return the number of rows in the section.
        return (mapInfoExchangeArray.count)
    }
    
    
    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("MarkerOfferCells", forIndexPath: indexPath) as! UITableViewCell
        var offer = mapInfoExchangeArray[indexPath.row]
        cell.textLabel!.text = offer
        
        // need to check if the offer contains the current logged in user
        var offerID = mapInfoExchangeIDArray[indexPath.row]
        
        // check if current user has already made that request
        var getExchangeWithID = "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/php/getExchangeById.php?exchangeId=" + offerID

        println(getExchangeWithID)
        // pull exchange information from server and check if user has made a request for it
        let url = NSURL(string: getExchangeWithID)
        
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            //println(NSString(data: data, encoding: NSUTF8StringEncoding))
            
            var arrayExchangeString = (json[0]["associatedExchanges"].string)
            
            if let dataFromString = arrayExchangeString!.dataUsingEncoding(NSUTF8StringEncoding, allowLossyConversion: false) {
                let jsonExchange = JSON(data: dataFromString)
                
                var index = 0
                for (key: String, subJson: JSON) in jsonExchange {
                    if (jsonExchange[index].string == self.appNetID) {
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
        
        // Configure the cell...
    }
    
    // if you select a cell, make the request and change how the cell is displayed
    override func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath) {
        let cell = tableView.cellForRowAtIndexPath(indexPath)
        if cell!.accessoryType != UITableViewCellAccessoryType.Checkmark
        {
            cell!.accessoryType = UITableViewCellAccessoryType.Checkmark
            cell!.selectionStyle = UITableViewCellSelectionStyle.None

            var pursueOfferString = "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/php/pursueOffer.php?"
            pursueOfferString += "netId=" + appNetID + "&offerId=" + mapInfoExchangeIDArray[indexPath.row]
            //println(pursueOfferString)
            
            // make a request to an offer (passes current user netid and desired offer id)
            let url = NSURL(string: pursueOfferString)
            
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                //println(NSString(data: data, encoding: NSUTF8StringEncoding))
                dispatch_async(dispatch_get_main_queue()) {
                    
                }
            }
            task.resume()
        }
    }

    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        // Get the new view controller using segue.destinationViewController.
        // Pass the selected object to the new view controller.
    }
    */

}
