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

    var offerClubNumberArray:[String] = []
    var offerDateArray:[String] = []
    var offerIDArray:[String] = []
    var requestClubNumberArray:[String] = []
    var requestDateArray:[String] = []
    var requestAssociatedIDArray:[String] = []


    override func viewDidLoad() {
        super.viewDidLoad()

        let appDelegate = UIApplication.sharedApplication().delegate as! AppDelegate
        appNetID = appDelegate.userNetid

        activeExchangeTableView.dataSource = self
        activeExchangeTableView.delegate = self
        
        userActiveExchangesPull()

        // Do any additional setup after loading the view.
    }
    
    func userActiveExchangesPull() {
        offerClubNumberArray.removeAll()
        offerDateArray.removeAll()
        offerIDArray.removeAll()
        requestClubNumberArray.removeAll()
        requestDateArray.removeAll()
        requestAssociatedIDArray.removeAll()
        
        
        var getActiveExchangesString = "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/php/userActiveExchanges.php?"
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
                    self.requestAssociatedIDArray.append(passID)
                }
                
                dispatch_async(dispatch_get_main_queue()) {
                    self.activeExchangeTableView.reloadData()
                }
            }
            /*
            println("****************offer Club Number*******************")
            println(self.offerClubNumberArray)
            println("****************request Club Number*******************")
            println(self.requestClubNumberArray)
            */
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
        return 2
    }
    
    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // Return the number of rows in the section.
        if section == 0 {
            return self.offerClubNumberArray.count
        } else if section == 1 {
            return self.requestClubNumberArray.count
        }
        return 0
    }

    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("ExchangeCell", forIndexPath: indexPath) as! UITableViewCell
        if indexPath.section == 0 { //offer stuff - Set title as the club and number, and subtitle as the date
            cell.textLabel!.text = offerClubNumberArray[indexPath.row]
            cell.detailTextLabel!.text = offerDateArray[indexPath.row]
            cell.accessoryType = UITableViewCellAccessoryType.DisclosureIndicator
        } else if indexPath.section == 1 { // request stuff
            // Set title as the club and number, and subtitle as the date
            cell.textLabel!.text = requestClubNumberArray[indexPath.row]
            cell.detailTextLabel!.text = requestDateArray[indexPath.row]
            cell.accessoryType = UITableViewCellAccessoryType.None
            
            //let acceptButton = UIButton.buttonWithType(UIButtonType.System) as! UIButton
            //acceptButton.titleLabel!.text = "Accept"
            //cell.addSubview(acceptButton)
        }
        
        // Configure the cell...
        
        return cell
    }
    
    // if you select a cell, make the request and change how the cell is displayed
    override func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath) {
        let cell = tableView.cellForRowAtIndexPath(indexPath)
        if indexPath.section == 0 {
            offerMoreInfoWindowID = offerIDArray[indexPath.row]
            offerMoreInfoWindowTitle = offerClubNumberArray[indexPath.row] + " " + offerDateArray[indexPath.row]
            performSegueWithIdentifier("offerAcceptDeclinePop", sender: self)
        } else if indexPath.section == 1 {
            
        }
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
        
        var exchangeString = "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/php/addExchange.php?"
        exchangeString += "netId=" + appNetID + "&passDate=" + formattedDateString + "&type=Offer" + "&numPasses=" + numPassesString + "&club=" + clubString + "&comment=" + ""
        //println(exchangeString)
        
        if (validRequest) {
            let url = NSURL(string: exchangeString)
            
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                //println(NSString(data: data, encoding: NSUTF8StringEncoding))
                
                dispatch_async(dispatch_get_main_queue()) {
                    self.userActiveExchangesPull()
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
