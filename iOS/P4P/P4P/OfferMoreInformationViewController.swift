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
    var offerAssociatedNetIDs:[String] = []

    override func viewDidLoad() {
        super.viewDidLoad()

        passRelatedRequests()

        // Do any additional setup after loading the view.
    }

    func passRelatedRequests() {
        // check if current user has already made that request
        var getExchangeWithID = "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/php/getExchangeById.php?exchangeId=" + offerMoreInfoID
        
        // pull exchange information from server and check if user has made a request for it
        let url = NSURL(string: getExchangeWithID)
        
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            
            var arrayExchangeString = (json[0]["associatedExchanges"].string)
            
            if let dataFromString = arrayExchangeString!.dataUsingEncoding(NSUTF8StringEncoding, allowLossyConversion: false) {
                let jsonExchange = JSON(data: dataFromString)
                
                var index = 0
                for (key: String, subJson: JSON) in jsonExchange {
                    self.offerAssociatedNetIDs.append(jsonExchange[index].string!)
                    index++
                }
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
        return count(offerAssociatedNetIDs)
    }

    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("OfferAcceptReject", forIndexPath: indexPath) as! UITableViewCell
        cell.textLabel!.text = offerAssociatedNetIDs[indexPath.row]
        //cell.accessoryType = UITableViewCellAccessoryType.DisclosureIndicator
        // Configure the cell...
        
        return cell
    }

    override func tableView(tableView: UITableView, commitEditingStyle editingStyle: UITableViewCellEditingStyle, forRowAtIndexPath indexPath: NSIndexPath) {
    }
    
    // ioscreator.com/tutorials/swipe-table-view-cell-custom-actions-tutorial-ios8-swift
    override func tableView(tableView: UITableView, editActionsForRowAtIndexPath indexPath: NSIndexPath) -> [AnyObject]?  {

        var acceptAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Accept" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
            println("you pressed accept")
            // do stuff for accepting - need to as a result reject all others (not sure if php script does ask artur)
        })

        var rejectAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Reject" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
            println("you pressed reject")
            // do stuff for rejecting
        })

        var chatAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Chat" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
            println("you pressed reject")
            // do stuff for linking to chat panel with user
        })

        
        // in both cases, need to reload data after doing thing with more or less things.
        
        acceptAction.backgroundColor = UIColor.greenColor()
        rejectAction.backgroundColor = UIColor.redColor()
        
        return [acceptAction,rejectAction, chatAction]
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
