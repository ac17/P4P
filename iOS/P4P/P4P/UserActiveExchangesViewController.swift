//
//  UserActiveExchangesViewController.swift
//  P4P
//
//  Created by Daniel Yang on 5/5/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//
//  View Controller that manages all the exchanges listed in the exchanges tab including pending trades
//  waiting for completion, a user's posted open offers, and a user's requests
//

import UIKit
import SwiftyJSON

class UserActiveExchangesViewController: UITableViewController, UIPopoverPresentationControllerDelegate {

    // global variables from app delegate
    var appNetID = ""
    var websiteURLbase = ""
    var keychainWrapper:KeychainWrapper! // necessary reference for use when logging out

    // global variables
    @IBOutlet var activeExchangeTableView: UITableView!
    var popoverViewController: PopupForAddExchangeViewController!               // for addExchange functionality
    
    // support for the window that pops up when you click on your open offer- accept/reject requests from people
    var offerAcceptRejectWindowNavigationController: UINavigationController!
    var offerMoreInfoWindowViewController: OfferMoreInformationViewController!
    var offerMoreInfoWindowTitle = ""
    var offerMoreInfoWindowID = ""

    // open offers you've posted
    var offerClubNumberArray:[String] = []
    var offerDateArray:[String] = []
    var offerIDArray:[String] = []
    
    // pending requests you've made
    var requestClubNumberArray:[String] = []
    var requestDateArray:[String] = []
    var requestIDArray:[String] = []
    
    // information about trades pending completion
    var activeTradesOfferIDArray:[String] = []
    var activeTradesRequestIDArray:[String] = []
    var activeTradesProviderNetIDArray:[String] = []
    var activeTradesProviderNameArray:[String] = []
    var activeTradesRecipientNetIDArray:[String] = []
    var activeTradesRecipientNameArray:[String] = []
    var activeTradesClubArray:[String] = []
    var activeTradesNumPassesArray:[String] = []
    var activeTradesDateArray:[String] = []
    
    // trying to minimze data reloads
    var globalFlagForReturnFrom = false


    override func viewDidLoad() {
        super.viewDidLoad()

        // pull information from app delegate
        let appDelegate = UIApplication.sharedApplication().delegate as! AppDelegate
        appNetID = appDelegate.userNetid
        websiteURLbase = appDelegate.websiteURLBase
        keychainWrapper = appDelegate.keychainWrapper

        // set tableView data souce and delegate
        activeExchangeTableView.dataSource = self
        activeExchangeTableView.delegate = self
    }
    
    // trying to minimze calls to reload data
    override func viewWillAppear(animated: Bool) {
        if (!globalFlagForReturnFrom) {
            userActiveExchangesPull()
            userActiveTradesPull()
        }
        globalFlagForReturnFrom = false
    }
    
    // reload all active trades a person is involved in
    func userActiveTradesPull() {
        // clear current data
        activeTradesOfferIDArray.removeAll()
        activeTradesRequestIDArray.removeAll()
        activeTradesProviderNetIDArray.removeAll()
        activeTradesProviderNameArray.removeAll()
        activeTradesRecipientNetIDArray.removeAll()
        activeTradesRecipientNameArray.removeAll()
        activeTradesClubArray.removeAll()
        activeTradesNumPassesArray.removeAll()
        activeTradesDateArray.removeAll()

        // generate HTTP request string
        var getActiveTradesString = self.websiteURLbase + "/php/userActiveTrades.php?"
        getActiveTradesString += "currentUserNetId=" + appNetID
        
        // pull info, parse JSON and categorize info into offer/request arrays
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
    
    // reload all active exchanges (open offers, pending requests) a person is involved in
    func userActiveExchangesPull() {
        // clear current data
        offerClubNumberArray.removeAll()
        offerDateArray.removeAll()
        offerIDArray.removeAll()
        
        requestClubNumberArray.removeAll()
        requestDateArray.removeAll()
        requestIDArray.removeAll()
        
        // generate HTTP request string
        var getActiveExchangesString = self.websiteURLbase + "/php/userActiveExchanges.php?"
        getActiveExchangesString += "currentUserNetId=" + appNetID
        
        // pull info from server, parse JSON and categorize info into offer/request arrays
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

        // active trade cells - need to generate a sentence telling relation of trade pending completion
        if indexPath.section == 0 {
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
        } else if indexPath.section == 1 { //offer table
            //Set title as the club and number, and subtitle as the date
            cell.textLabel!.text = offerClubNumberArray[indexPath.row]
            cell.detailTextLabel!.text = offerDateArray[indexPath.row]
            cell.accessoryType = UITableViewCellAccessoryType.DisclosureIndicator
            cell.selectionStyle = UITableViewCellSelectionStyle.None
        } else if indexPath.section == 2 { // request table
            // Set title as the club and number, and subtitle as the date
            cell.textLabel!.text = requestClubNumberArray[indexPath.row]
            cell.detailTextLabel!.text = requestDateArray[indexPath.row]
            cell.selectionStyle = UITableViewCellSelectionStyle.None
            cell.accessoryType = UITableViewCellAccessoryType.None
        }
        
        return cell
    }
    
    // if you select a cell....
    override func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath) {
        let cell = tableView.cellForRowAtIndexPath(indexPath)
        
        if indexPath.section == 0 { // active trades
            // doesn't matter if selected; do nothing
        } else if indexPath.section == 1 { // offers
            // make popup with people who have requested your offer that allows for you to accept/reject
            offerMoreInfoWindowID = offerIDArray[indexPath.row]
            offerMoreInfoWindowTitle = offerClubNumberArray[indexPath.row] + " " + offerDateArray[indexPath.row]
            performSegueWithIdentifier("offerAcceptDeclinePop", sender: self)
        } else if indexPath.section == 2 { // requests
            // doesn't matter if selected; do nothing
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
    
    // part of making swipe left and right cell functionality
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
        if indexPath.section == 0 { // trades
            
            // notify system that a trade is copmleted
            var completeTrade = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Completed" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
                
                // generate necessary HTTP request url
                var completeTradeURL = self.websiteURLbase + "/php/completeTrade.php?currentUserNetId=" + self.appNetID + "&provider=" + self.activeTradesProviderNetIDArray[indexPath.row] + "&recipient=" + self.activeTradesRecipientNetIDArray[indexPath.row] + "&offerId=" + self.activeTradesOfferIDArray[indexPath.row] + "&requestId=" + self.activeTradesRequestIDArray[indexPath.row]
                
                let url = NSURL(string: completeTradeURL)
                let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                    let json = JSON(data: data)
                    
                    dispatch_async(dispatch_get_main_queue()) {
                        // reload data
                        self.userActiveTradesPull()
                        self.activeExchangeTableView.reloadData()
                    }
                }
                task.resume()
            })
            
            // notify system of a trade cancellation
            var cancelTrade = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Cancel" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
                
                // generate necessary HTTP request URL
                var cancelTradeURL = self.websiteURLbase + "/php/cancelTrade.php?currentUserNetId=" + self.appNetID + "&provider=" + self.activeTradesProviderNetIDArray[indexPath.row] + "&recipient=" + self.activeTradesRecipientNetIDArray[indexPath.row] + "&offerId=" + self.activeTradesOfferIDArray[indexPath.row] + "&requestId=" + self.activeTradesRequestIDArray[indexPath.row]
                
                let url = NSURL(string: cancelTradeURL)
                let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                    let json = JSON(data: data)
                    
                    dispatch_async(dispatch_get_main_queue()) {
                        // reload data
                        self.userActiveTradesPull()
                        self.activeExchangeTableView.reloadData()
                    }
                }
                task.resume()
            })
            
            // chat with person involved in exchange
            var chatAction = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Chat" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in
                self.tabBarController!.selectedIndex = 2
                
                var userID = ""
                if self.activeTradesProviderNetIDArray[indexPath.row] == self.appNetID { // user is provider
                    userID = self.activeTradesRecipientNetIDArray[indexPath.row]
                } else if self.activeTradesRecipientNetIDArray[indexPath.row] == self.appNetID { // user is recipient
                    userID = self.activeTradesProviderNetIDArray[indexPath.row]
                }
                
                for index in 0...2 {
                    if let controller = self.tabBarController!.viewControllers![index] as? UINavigationController {
                        if let chatController = controller.topViewController as? ChatViewController {
                            chatController.sidePanelCurrentlySelectedUser = userID
                        }
                    }
                }
            })
            
            // set button colors
            completeTrade.backgroundColor = UIColor.greenColor()
            cancelTrade.backgroundColor = UIColor.redColor()
            chatAction.backgroundColor = UIColor.blueColor()
            
            return [completeTrade, cancelTrade, chatAction]
            
        } else if indexPath.section == 1 { // offers
            
            // delete an offer from the system
            var deleteOffer = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Delete" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in

                // generate HTTP request URL
                var deleteOfferURL = self.websiteURLbase + "/php/deleteOffer.php?offerId=" + self.offerIDArray[indexPath.row] + "&requesterNetId=" + self.appNetID

                let url = NSURL(string: deleteOfferURL)
                let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                    let json = JSON(data: data)
                    
                    dispatch_async(dispatch_get_main_queue()) {
                        // reload data
                        self.userActiveExchangesPull()
                        self.activeExchangeTableView.reloadData()
                    }
                }
                task.resume()
            })
            
            // set background color of button
            deleteOffer.backgroundColor = UIColor.redColor()
            return [deleteOffer]
        } else if indexPath.section == 2 { // requests
           
            // delete a request from the system
            var deleteRequest = UITableViewRowAction(style: UITableViewRowActionStyle.Default, title: "Delete" , handler: { (action:UITableViewRowAction!, indexPath:NSIndexPath!) -> Void in

                // generate HTTP request URL
                var deleteRequestURL = self.websiteURLbase + "/php/deleteRequest.php?requestId=" + self.requestIDArray[indexPath.row] + "&requesterNetId=" + self.appNetID

                let url = NSURL(string: deleteRequestURL)
                let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                    let json = JSON(data: data)
                    
                    dispatch_async(dispatch_get_main_queue()) {
                        // reload data
                        self.userActiveExchangesPull()
                        self.activeExchangeTableView.reloadData()
                    }
                }
                task.resume()
            })
            // set background color of button
            deleteRequest.backgroundColor = UIColor.redColor()
            return [deleteRequest]
        }
        return nil
    }
    
    /***** popover for creating an exchange ****/
    // create exchange button pressed on popup
    @IBAction func addExchangePopup(segue:UIStoryboardSegue)
    {
        var validRequest = true // used to check all fields were filled in
        
        // retrieve data from fields
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
        
        // generate HTTP request URL
        var exchangeString = self.websiteURLbase + "/php/addExchange.php?"
        exchangeString += "netId=" + appNetID + "&passDate=" + formattedDateString + "&type=Offer" + "&numPasses=" + numPassesString + "&club=" + clubString + "&comment=" + ""
        
        if (validRequest) {
            let url = NSURL(string: exchangeString)
            
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                dispatch_async(dispatch_get_main_queue()) {
                    // reload data
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
        } else if segue.identifier == "returnToLogInScreen" {
            self.parentViewController!.dismissViewControllerAnimated(true, completion: nil)
        }
    }
    
    // has to be a popover; otherwise unaccepted
    func adaptivePresentationStyleForPresentationController(controller: UIPresentationController) -> UIModalPresentationStyle {
        return UIModalPresentationStyle.None
    }

    // allow for returning to user active exchanges view controller w/o reload of data
    @IBAction func returnToUserActiveExchanges(segue:UIStoryboardSegue) {
        self.globalFlagForReturnFrom = true
    }

    // allow for returning to user active exchanges view controller w/ reload of data
    @IBAction func returnToUserActiveExchangesWithReload(segue:UIStoryboardSegue) {
        self.globalFlagForReturnFrom = false
    }

    // allow for returning to user active exchanges view controller and then go to chat
    @IBAction func returnBeforeCallingChat (segue:UIStoryboardSegue) {
        self.tabBarController!.selectedIndex = 2
        for index in 0...2 {
            if let controller = self.tabBarController!.viewControllers![index] as? UINavigationController {
                if let chatController = controller.topViewController as? ChatViewController {
                    chatController.sidePanelCurrentlySelectedUser = self.offerMoreInfoWindowViewController.offerNetID
                }
            }
        }
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }

    // clear data stored in keychain if user logs out
    @IBAction func logoutAccount(sender: AnyObject) {
        keychainWrapper.mySetObject(nil, forKey: kSecAttrAccount)
        keychainWrapper.mySetObject(nil, forKey: kSecValueData)
        NSUserDefaults.standardUserDefaults().setValue(false, forKey: "hasLoginKey")
        NSUserDefaults.standardUserDefaults().synchronize()
        
        self.performSegueWithIdentifier("returnToLogInScreen", sender: self)
    }
}
