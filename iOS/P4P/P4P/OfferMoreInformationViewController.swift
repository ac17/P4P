//
//  OfferMoreInformationViewController.swift
//  P4P
//
//  Created by Daniel Yang on 5/6/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit
import SwiftyJSON

class OfferMoreInformationViewController: UITableViewController {
    
    @IBOutlet var offerMoreInfoTableView: UITableView!
    var offerMoreInfoID: String = ""

    var appNetID = ""
    var websiteURLbase = ""

    var offerAssociatedNetIDs:[String] = []
    var offerAssociatedNames:[String] = []

    override func viewDidLoad() {
        super.viewDidLoad()

        let appDelegate = UIApplication.sharedApplication().delegate as! AppDelegate
        appNetID = appDelegate.userNetid
        websiteURLbase = appDelegate.websiteURLBase

        passRelatedRequests()

        // Do any additional setup after loading the view.
    }

    func passRelatedRequests() {
        offerAssociatedNetIDs.removeAll()
        offerAssociatedNames.removeAll()

        // check if current user has already made that request
        var getExchangeWithID = self.websiteURLbase + "/php/getExchangeById.php?exchangeId=" + offerMoreInfoID
        println(getExchangeWithID)
        // pull exchange information from server and check if user has made a request for it
        let url = NSURL(string: getExchangeWithID)
        
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            
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
        // #warning Potentially incomplete method implementation.
        // Return the number of sections.
        return 1
    }
    
    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // Return the number of rows in the section.
        return count(offerAssociatedNames)
    }

    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("OfferAcceptReject", forIndexPath: indexPath) as! UITableViewCell
        cell.textLabel!.text = offerAssociatedNames[indexPath.row]
        //cell.accessoryType = UITableViewCellAccessoryType.DisclosureIndicator
        // Configure the cell...
        
        return cell
    }

    override func tableView(tableView: UITableView, commitEditingStyle editingStyle: UITableViewCellEditingStyle, forRowAtIndexPath indexPath: NSIndexPath) {
    }
    
    // swipe left and right to generate buttons on a table cell
    override func tableView(tableView: UITableView, editActionsForRowAtIndexPath indexPath: NSIndexPath) -> [AnyObject]?  {

        var acceptAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Accept" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
            // do stuff for accepting - also rejects all others on the backend
            var acceptRequest = self.websiteURLbase + "/php/acceptRequest.php?offerId=" + self.offerMoreInfoID + "&requesterNetId=" + self.offerAssociatedNetIDs[indexPath.row] + "&currentUserNetId=" + self.appNetID
            
            // pull exchange information from server and check if user has made a request for it
            let url = NSURL(string: acceptRequest)
            
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                let json = JSON(data: data)
                
                dispatch_async(dispatch_get_main_queue()) {
                    self.performSegueWithIdentifier("returnToActiveExchangesWithReload", sender: self)
                }
                
            }
            task.resume()
        })

        var rejectAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Decline" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
            // do stuff for rejecting
            var rejectRequest = self.websiteURLbase + "/php/declineRequest.php?offerId=" + self.offerMoreInfoID + "&requesterNetId=" + self.offerAssociatedNetIDs[indexPath.row] + "&currentUserNetId=" + self.appNetID
            
            // pull exchange information from server and check if user has made a request for it
            let url = NSURL(string: rejectRequest)
            
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                let json = JSON(data: data)
                
                dispatch_async(dispatch_get_main_queue()) {
                    self.reloadDataAndTable()
                }
                
            }
            task.resume()

        })

        var chatAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Chat" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
            println("you pressed chat")
            // do stuff for linking to chat panel with user
            self.performSegueWithIdentifier("returnThenChat", sender: self)
        })

        
        // in both cases, need to reload data after doing thing with more or less things.
        
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



    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        // Get the new view controller using segue.destinationViewController.
        // Pass the selected object to the new view controller.
    }
    */

}
