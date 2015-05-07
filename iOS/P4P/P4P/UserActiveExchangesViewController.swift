//
//  UserActiveExchangesViewController.swift
//  P4P
//
//  Created by Daniel Yang on 5/5/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit
import SwiftyJSON

class UserActiveExchangesViewController: UITableViewController, UIPopoverPresentationControllerDelegate {

    @IBOutlet var activeExchangeTableView: UITableView!
    var popoverViewController: PopupForAddExchangeViewController!
    
    var offerAcceptRejectWindowNavigationController: UINavigationController!
    var offerMoreInfoWindowViewController: OfferMoreInformationViewController!
    var offerMoreInfoWindowTitle = ""
    var offerMoreInfoWindowID = ""

    var appNetID = ""
    var websiteURLbase = ""

    var offerClubNumberArray:[String] = []
    var offerDateArray:[String] = []
    var offerIDArray:[String] = []
    
    var requestClubNumberArray:[String] = []
    var requestDateArray:[String] = []
    var requestIDArray:[String] = []
    
    var activeTradesOfferIDArray:[String] = []
    var activeTradesRequestIDArray:[String] = []
    var activeTradesProviderNetIDArray:[String] = []
    var activeTradesProviderNameArray:[String] = []
    var activeTradesRecipientNetIDArray:[String] = []
    var activeTradesRecipientNameArray:[String] = []
    var activeTradesClubArray:[String] = []
    var activeTradesNumPassesArray:[String] = []
    var activeTradesDateArray:[String] = []
    
    var backgroundView: UIImageView?


    override func viewDidLoad() {
        super.viewDidLoad()

        let appDelegate = UIApplication.sharedApplication().delegate as! AppDelegate
        appNetID = appDelegate.userNetid
        websiteURLbase = appDelegate.websiteURLBase
        
        /*
        // Set background color to dark blue
        backgroundView = UIImageView(image: UIImage(named: "darkbluebackground.png"))
        backgroundView!.frame = UIScreen.mainScreen().bounds
        self.view.insertSubview(backgroundView!, atIndex: 0)
        */
        
        activeExchangeTableView.dataSource = self
        activeExchangeTableView.delegate = self
        
        // Do any additional setup after loading the view.
    }
    
    override func viewWillAppear(animated: Bool) {
        userActiveExchangesPull()
        userActiveTradesPull()
    }
    
    func userActiveTradesPull() {
        activeTradesOfferIDArray.removeAll()
        activeTradesRequestIDArray.removeAll()
        activeTradesProviderNetIDArray.removeAll()
        activeTradesProviderNameArray.removeAll()
        activeTradesRecipientNetIDArray.removeAll()
        activeTradesRecipientNameArray.removeAll()
        activeTradesClubArray.removeAll()
        activeTradesNumPassesArray.removeAll()
        activeTradesDateArray.removeAll()

        var getActiveTradesString = self.websiteURLbase + "/php/userActiveTrades.php?"
        getActiveTradesString += "currentUserNetId=" + appNetID
        //println(getActiveTradesString)
        
        // pull info from server of all active trades, categorize info into offer/request arrays
        let url = NSURL(string: getActiveTradesString)
        
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            for (informationExchange:String, subJsonExchange: JSON) in json["Trades"] {
                var offerID = "007"
                var requestID = "888"
                var providerNetId = "JBond"
                var providerName = "James"
                var requesterNetId = "BSmith"
                var requesterName = "Bob"
                var club = "Chocolate"
                var numPasses = "-1"
                var date = "1995-11-17"
                
                if let temp = subJsonExchange["offerId"].string { offerID = temp }
                if let temp = subJsonExchange["requestId"].string { requestID = temp }
                if let temp = subJsonExchange["provider"].string { providerNetId = temp }
                if let temp = subJsonExchange["providerName"].string { providerName = temp }
                if let temp = subJsonExchange["recipient"].string { requesterNetId = temp }
                if let temp = subJsonExchange["recipientName"].string { requesterName = temp }
                if let temp = subJsonExchange["club"].string { club = temp }
                if let temp = subJsonExchange["passNum"].string { numPasses = temp }
                if let temp = subJsonExchange["passDate"].string { date = temp }
                
                self.activeTradesOfferIDArray.append(offerID)
                self.activeTradesRequestIDArray.append(requestID)
                self.activeTradesProviderNetIDArray.append(providerNetId)
                self.activeTradesProviderNameArray.append(providerName)
                self.activeTradesRecipientNetIDArray.append(requesterNetId)
                self.activeTradesRecipientNameArray.append(requesterName)
                self.activeTradesClubArray.append(club)
                self.activeTradesNumPassesArray.append(numPasses)
                self.activeTradesDateArray.append(date)
                
                dispatch_async(dispatch_get_main_queue()) {
                    self.activeExchangeTableView.reloadData()
                }
            }
        }
        task.resume()

    }
    
    
    func userActiveExchangesPull() {
        offerClubNumberArray.removeAll()
        offerDateArray.removeAll()
        offerIDArray.removeAll()
        
        requestClubNumberArray.removeAll()
        requestDateArray.removeAll()
        requestIDArray.removeAll()
        
        var getActiveExchangesString = self.websiteURLbase + "/php/userActiveExchanges.php?"
        getActiveExchangesString += "currentUserNetId=" + appNetID
        //println(getActiveExchangesString)
        
        // pull info from server of all exchanges, categorize info into offer/request arrays
        let url = NSURL(string: getActiveExchangesString)
        
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            for (informationExchange:String, subJsonExchange: JSON) in json["Exchanges"] {
                var passID = "007"
                var passClub = "Chocolate"
                var passNumber = "-1"
                var passDate = "1995-11-17"
                var passType = "offerquest"
                
                if let temp = subJsonExchange["id"].string { passID = temp }
                if let temp = subJsonExchange["club"].string { passClub = temp }
                if let temp = subJsonExchange["passNum"].string { passNumber = temp }
                if let temp = subJsonExchange["passDate"].string { passDate = temp }
                if let temp = subJsonExchange["type"].string { passType = temp }
                
                if (passType == "Offer") {
                    var clubNumberString = passClub + " (" + passNumber + ")"
                    self.offerClubNumberArray.append(clubNumberString)
                    self.offerDateArray.append(passDate)
                    self.offerIDArray.append(passID)
                    
                } else if (passType == "Request") {
                    var clubNumberString = passClub + " (" + passNumber + ")"
                    self.requestClubNumberArray.append(clubNumberString)
                    self.requestDateArray.append(passDate)
                    self.requestIDArray.append(passID)
                }
                
                dispatch_async(dispatch_get_main_queue()) {
                    self.activeExchangeTableView.reloadData()
                }
            }
        }
        task.resume()
    }

    override func viewDidAppear(animated: Bool) {
        var tabBarController = self.tabBarController as! TabBarViewController
        tabBarController.lastScreen = 0
    }

    override func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        // #warning Potentially incomplete method implementation.
        // Return the number of sections.
        return 3
    }
    
    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // Return the number of rows in the section.
        if section == 0 {
            return self.activeTradesClubArray.count
        } else if section == 1 {
            return self.offerClubNumberArray.count
        } else if section == 2 {
            return self.requestClubNumberArray.count
        }
        return 0
    }

    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("ExchangeCell", forIndexPath: indexPath) as! UITableViewCell

        if indexPath.section == 0 { // active trade stuff
            var textLabelString = ""
            var exchangeContents = self.activeTradesClubArray[indexPath.row] + " (" + self.activeTradesNumPassesArray[indexPath.row] + ")"
            var providerString = self.activeTradesProviderNameArray[indexPath.row] + " (" + self.activeTradesProviderNetIDArray[indexPath.row] + ")"
            var receiverString = self.activeTradesRecipientNameArray[indexPath.row] + " (" + self.activeTradesRecipientNetIDArray[indexPath.row] + ")"
            
            if self.activeTradesProviderNetIDArray[indexPath.row] == self.appNetID { // user is provider
                textLabelString += "Providing " + exchangeContents + " to " + receiverString
            } else if self.activeTradesRecipientNetIDArray[indexPath.row] == self.appNetID { // user is recipient
                textLabelString += "Receiving " + exchangeContents + " from " + providerString
            }
            
            cell.textLabel!.text = textLabelString
            cell.detailTextLabel!.text = activeTradesDateArray[indexPath.row]
            cell.selectionStyle = UITableViewCellSelectionStyle.None
            cell.accessoryType = UITableViewCellAccessoryType.None
            println("hello")
            println("string" + textLabelString)
            println("appNetID" + appNetID)
        } else if indexPath.section == 1 { //offer stuff - Set title as the club and number, and subtitle as the date
            cell.textLabel!.text = offerClubNumberArray[indexPath.row]
            cell.detailTextLabel!.text = offerDateArray[indexPath.row]
            cell.accessoryType = UITableViewCellAccessoryType.DisclosureIndicator
        } else if indexPath.section == 2 { // request stuff
            // Set title as the club and number, and subtitle as the date
            cell.textLabel!.text = requestClubNumberArray[indexPath.row]
            cell.detailTextLabel!.text = requestDateArray[indexPath.row]
            cell.selectionStyle = UITableViewCellSelectionStyle.None
            cell.accessoryType = UITableViewCellAccessoryType.None
        }
        
        // Configure the cell...
        /*
        cell.contentView.backgroundColor = UIColor(netHex: 0x1FBAD6)
        cell.textLabel!.textColor = UIColor.whiteColor()
        cell.detailTextLabel?.textColor = UIColor.whiteColor()
        */
        
        return cell
    }
    
    // if you select a cell, make the request and change how the cell is displayed
    override func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath) {
        let cell = tableView.cellForRowAtIndexPath(indexPath)
        
        if indexPath.section == 0 { // active trades
            
        } else if indexPath.section == 1 { // offers
            offerMoreInfoWindowID = offerIDArray[indexPath.row]
            offerMoreInfoWindowTitle = offerClubNumberArray[indexPath.row] + " " + offerDateArray[indexPath.row]
            performSegueWithIdentifier("offerAcceptDeclinePop", sender: self)
        } else if indexPath.section == 2 { // requests
            
        }
    }
    
    // name each section
    override func tableView(tableView: UITableView, titleForHeaderInSection section: Int) -> String? {
        if section == 0 {
            return "Trades"
        } else if section == 1 {
            return "Your Open Offers"
        } else if section == 2 {
            return "Your Pending Requests"
        }
        return ""
    }
    
    // part of making swipe left and right
    override func tableView(tableView: UITableView, commitEditingStyle editingStyle: UITableViewCellEditingStyle, forRowAtIndexPath indexPath: NSIndexPath) {
    }
    
    
    // Add a separator between sections of the table
    override func tableView(tableView: UITableView, willDisplayHeaderView view: UIView, forSection section: Int) {
        var headerView: UITableViewHeaderFooterView = view as! UITableViewHeaderFooterView

        var sepFrame: CGRect = CGRectMake(0, view.frame.size.height-1, view.frame.size.width, 2)
        let seperatorView = UIView(frame: sepFrame)
        seperatorView.backgroundColor = UIColor(white: 224.0/255.0, alpha:1.0)
        headerView.addSubview(seperatorView)
    }

    // swipe left and right to generate buttons on a table cell
    override func tableView(tableView: UITableView, editActionsForRowAtIndexPath indexPath: NSIndexPath) -> [AnyObject]?  {
        if indexPath.section == 0 { // active trades
            var completeTrade = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Completed" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
                // do stuff for accepting - also rejects all others on the backend
                var completeTrade = self.websiteURLbase + "/php/completeTrade.php?currentUserNetId=" + self.appNetID + "&provider=" + self.activeTradesProviderNetIDArray[indexPath.row] + "&recipient=" + self.activeTradesRecipientNetIDArray[indexPath.row] + "&offerId=" + self.activeTradesOfferIDArray[indexPath.row] + "&requestId=" + self.activeTradesRequestIDArray[indexPath.row]
                
                // pull exchange information from server and check if user has made a request for it
                let url = NSURL(string: completeTrade)
                
                let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                    let json = JSON(data: data)
                    
                    dispatch_async(dispatch_get_main_queue()) {
                        self.userActiveTradesPull()
                        self.activeExchangeTableView.reloadData()
                    }
                }
                task.resume()
            })
            
            var cancelTrade = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Cancel" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
                // do stuff for cancelling a trade
                
                var cancelTrade = self.websiteURLbase + "/php/cancelTrade.php?currentUserNetId=" + self.appNetID + "&provider=" + self.activeTradesProviderNetIDArray[indexPath.row] + "&recipient=" + self.activeTradesRecipientNetIDArray[indexPath.row] + "&offerId=" + self.activeTradesOfferIDArray[indexPath.row] + "&requestId=" + self.activeTradesRequestIDArray[indexPath.row]
                
                // pull exchange information from server and check if user has made a request for it
                let url = NSURL(string: cancelTrade)
                
                let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                    let json = JSON(data: data)
                    
                    dispatch_async(dispatch_get_main_queue()) {
                        self.userActiveTradesPull()
                        self.activeExchangeTableView.reloadData()
                    }
                }
                task.resume()
            })
            
            var chatAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Chat" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
                self.tabBarController!.selectedIndex = 2
            })
            
            // in both cases, need to reload data after doing thing with more or less things.
            
            completeTrade.backgroundColor = UIColor.greenColor()
            cancelTrade.backgroundColor = UIColor.redColor()
            chatAction.backgroundColor = UIColor.blueColor()
            
            return [completeTrade, cancelTrade, chatAction]
            
        } else if indexPath.section == 1 { // offers
            var deleteOffer = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Delete" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
                // do stuff for cancelling on offer
                
                var deleteOfferURL = self.websiteURLbase + "/php/deleteOffer.php?offerId=" + self.offerIDArray[indexPath.row] + "&requesterNetId=" + self.appNetID
                println(deleteOfferURL)
                // pull exchange information from server and check if user has made a request for it
                let url = NSURL(string: deleteOfferURL)
                
                let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                    let json = JSON(data: data)
                    
                    dispatch_async(dispatch_get_main_queue()) {
                        self.userActiveExchangesPull()
                        self.activeExchangeTableView.reloadData()
                    }
                    
                }
                task.resume()
            })
            deleteOffer.backgroundColor = UIColor.redColor()
            return [deleteOffer]
        } else if indexPath.section == 2 { // requests
            var deleteRequest = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Delete" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
                // do stuff for cancelling a request

                var deleteRequestURL = self.websiteURLbase + "/php/deleteRequest.php?requestId=" + self.requestIDArray[indexPath.row] + "&requesterNetId=" + self.appNetID
                println(deleteRequestURL)
                // pull exchange information from server and check if user has made a request for it
                let url = NSURL(string: deleteRequestURL)
                
                let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                    let json = JSON(data: data)
                    
                    dispatch_async(dispatch_get_main_queue()) {
                        self.userActiveExchangesPull()
                        self.activeExchangeTableView.reloadData()
                    }
                    
                }
                task.resume()
            })
            deleteRequest.backgroundColor = UIColor.redColor()
            return [deleteRequest]
        }
        return nil
    }
    
    /***** popover for creating an exchange ****/
    
    // create exchange button pressed on popup
    @IBAction func addExchangePopup(segue:UIStoryboardSegue)
    {
        var validRequest = true
        var clubString = popoverViewController.clubField.text
        var dateString = popoverViewController.dateField.text
        var numPassesString = popoverViewController.numPassesField.text
        if (((clubString == "") || (dateString == "")) || (numPassesString == "")) {
            validRequest = false
        }
        
        // HTTP requests need format xx/yy/zz, not x/y/zz
        var formattedDateString = ""
        if !dateString.isEmpty {
            var dateStringArray = dateString.componentsSeparatedByString("/")
            if (count(dateStringArray[0]) == 1) {
                dateStringArray[0] = "0" + dateStringArray[0]
            }
            if (count(dateStringArray[1]) == 1) {
                dateStringArray[1] = "0" + dateStringArray[1]
            }
            if (count(dateStringArray[2]) == 2) {
                dateStringArray[2] = "20" + dateStringArray[2]
            }
            
            formattedDateString = dateStringArray[0] + "/" + dateStringArray[1] + "/" + dateStringArray[2]
        }
        
        // replace spaces in club name with pluses
        clubString = clubString.stringByReplacingOccurrencesOfString(" ", withString: "+", options: NSStringCompareOptions.LiteralSearch, range: nil)
        
        var exchangeString = self.websiteURLbase + "/php/addExchange.php?"
        exchangeString += "netId=" + appNetID + "&passDate=" + formattedDateString + "&type=Offer" + "&numPasses=" + numPassesString + "&club=" + clubString + "&comment=" + ""
        //println(exchangeString)
        
        if (validRequest) {
            let url = NSURL(string: exchangeString)
            
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                //println(NSString(data: data, encoding: NSUTF8StringEncoding))
                
                dispatch_async(dispatch_get_main_queue()) {
                    self.userActiveExchangesPull()
                    self.userActiveTradesPull()
                }
            }
            task.resume()
        }
        self.dismissViewControllerAnimated(true, completion: nil)
    }
    
    // specifics to happen when you call a segue
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        if segue.identifier == "popoverSegueAddExchange" {
            popoverViewController = segue.destinationViewController as! PopupForAddExchangeViewController
            popoverViewController.modalPresentationStyle = UIModalPresentationStyle.Popover
            popoverViewController.popoverPresentationController!.delegate = self
        }
        else if segue.identifier == "offerAcceptDeclinePop" {
            offerAcceptRejectWindowNavigationController = segue.destinationViewController as! UINavigationController
            offerMoreInfoWindowViewController = offerAcceptRejectWindowNavigationController.topViewController as! OfferMoreInformationViewController
            offerMoreInfoWindowViewController.title = offerMoreInfoWindowTitle
            offerMoreInfoWindowViewController.offerMoreInfoID = offerMoreInfoWindowID
        }
    }
    
    // has to be a popover; otherwise unaccepted
    func adaptivePresentationStyleForPresentationController(controller: UIPresentationController) -> UIModalPresentationStyle {
        return UIModalPresentationStyle.None
    }

    // allow for returning to user active exchanges view controller
    @IBAction func returnToUserActiveExchanges(segue:UIStoryboardSegue) {
    }
    
    @IBAction func returnBeforeCallingChat (segue:UIStoryboardSegue) {
        self.tabBarController!.selectedIndex = 2
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
