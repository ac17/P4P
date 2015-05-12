//
//  OfferMoreInformationViewController.swift
//  P4P
//
//  Created by Daniel Yang on 5/6/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//
//  View controller displays requests associated with an offer. Allows for accepting/rejecting the request
//

import UIKit
import SwiftyJSON

class OfferMoreInformationViewController: UITableViewController {
    
    // global variables from app delegate
    var appNetID = ""
    var websiteURLbase = ""

    // global variables
    @IBOutlet var offerMoreInfoTableView: UITableView!
    var offerMoreInfoID: String = ""

    // data for tableView
    var offerAssociatedNetIDs:[String] = []
    var offerAssociatedNames:[String] = []

    var offerNetID = ""
    
    override func viewDidLoad() {
        super.viewDidLoad()

        // retrieve data from app delegate
        let appDelegate = UIApplication.sharedApplication().delegate as! AppDelegate
        appNetID = appDelegate.userNetid
        websiteURLbase = appDelegate.websiteURLBase

        // retrieve data from server
        passRelatedRequests()

    }

    // get information relating to the offer- display requests made to offer
    func passRelatedRequests() {
        // clear data arrays
        offerAssociatedNetIDs.removeAll()
        offerAssociatedNames.removeAll()

        // generate HTTP request url for the specific offer
        var getExchangeWithID = self.websiteURLbase + "/php/getExchangeById.php?exchangeId=" + offerMoreInfoID

        let url = NSURL(string: getExchangeWithID)
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            
            // get all associated netIDs and names to that offer by parsing JSON
            var arrayExchangeString = (json[0]["associatedExchanges"].string)
            
            if let dataFromStringNetIDs = arrayExchangeString!.dataUsingEncoding(NSUTF8StringEncoding, allowLossyConversion: false) {
                let jsonExchange = JSON(data: dataFromStringNetIDs)
                
                var index = 0
                for (key: String, subJson: JSON) in jsonExchange {
                    self.offerAssociatedNetIDs.append(jsonExchange[index].string!)
                    index++
                }
            }

            var otherIndex = 0
            for (key: String, subJson:JSON) in json[0]["names"] {
                self.offerAssociatedNames.append(json[0]["names"][otherIndex].string!)
                otherIndex++
            }

            dispatch_async(dispatch_get_main_queue()) {
                self.offerMoreInfoTableView.reloadData()
            }
        }
        task.resume()
    }
    
    override func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        // Return the number of sections.
        return 1
    }
    
    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // Return the number of rows in the section.
        return count(offerAssociatedNames)
    }

    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        // return a cell with the title set as the name of the person making the request
        let cell = tableView.dequeueReusableCellWithIdentifier("OfferAcceptReject", forIndexPath: indexPath) as! UITableViewCell
        cell.textLabel!.text = offerAssociatedNames[indexPath.row]
        
        return cell
    }

    // necessary to be able to swipe left and right for a cell
    override func tableView(tableView: UITableView, commitEditingStyle editingStyle: UITableViewCellEditingStyle, forRowAtIndexPath indexPath: NSIndexPath) {
    }
    
    // swipe left and right to generate buttons on a table cell
    override func tableView(tableView: UITableView, editActionsForRowAtIndexPath indexPath: NSIndexPath) -> [AnyObject]?  {

        // accepting an offer! return back to the exchanges tab because you also reject all other requests
        var acceptAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Accept" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in

            // generate HTTP request url for the specific offer; execute
            var acceptRequest = self.websiteURLbase + "/php/acceptRequest.php?offerId=" + self.offerMoreInfoID + "&requesterNetId=" + self.offerAssociatedNetIDs[indexPath.row] + "&currentUserNetId=" + self.appNetID
            
            let url = NSURL(string: acceptRequest)
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                let json = JSON(data: data)
                
                dispatch_async(dispatch_get_main_queue()) {
                    self.performSegueWithIdentifier("returnToActiveExchangesWithReload", sender: self)
                }
            }
            task.resume()
        })

        // rejecting an offer :( should clear but stay in this view
        var rejectAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Decline" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
            
            // generate HTTP request url for the specific offer; execute
            var rejectRequest = self.websiteURLbase + "/php/declineRequest.php?offerId=" + self.offerMoreInfoID + "&requesterNetId=" + self.offerAssociatedNetIDs[indexPath.row] + "&currentUserNetId=" + self.appNetID
            
            let url = NSURL(string: rejectRequest)
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                let json = JSON(data: data)
                
                dispatch_async(dispatch_get_main_queue()) {
                    self.reloadDataAndTable()
                }
            }
            task.resume()
        })

        // chat with person making the request
        var chatAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Chat" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
            println("you pressed chat")
            self.offerNetID = self.offerAssociatedNetIDs[indexPath.row]
            self.performSegueWithIdentifier("returnThenChat", sender: self)
        })

        acceptAction.backgroundColor = UIColor.greenColor()
        rejectAction.backgroundColor = UIColor.redColor()
        chatAction.backgroundColor = UIColor.blueColor()
        
        return [acceptAction,rejectAction, chatAction]
    }

    func reloadDataAndTable() {
        passRelatedRequests()
        self.tableView.reloadData()
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
}
