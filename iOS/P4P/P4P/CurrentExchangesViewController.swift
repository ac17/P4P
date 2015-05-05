//
//  CurrentExchangesViewController.swift
//  P4P
//
//  Created by Frank Jiang on 21/4/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

struct Offer {
    var netid: String
    var number: Int
    var club: String
    var date: String
}

struct Request {
    var netid: String
    var number: Int
    var club: String
    var date: String
    var offer: Offer
}

class CurrentExchangesViewController: UITableViewController, UIPopoverPresentationControllerDelegate {

    var popoverViewController: PopupForAddExchangeViewController!
    var appNetID = ""

    var offers:[Offer] = []
    var requests:[Request] = []
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        let appDelegate = UIApplication.sharedApplication().delegate as! AppDelegate
        appNetID = appDelegate.userNetid
        
        offers.append(Offer(netid: "ffjiang", number: 2, club: "Colonial", date: "11/12/13"))
        offers.append(Offer(netid: "ffjiang", number: 2, club: "Colonial", date: "13/14/15"))
        offers.append(Offer(netid: "ffjiang", number: 2, club: "Colonial", date: "14/15/16"))
        requests.append(Request(netid: "dxyang", number: 2, club: "Cottage", date: "23/12/13", offer: offers[0]))
        requests.append(Request(netid: "dxyang", number: 2, club: "Cottage", date: "23/12/13", offer: offers[1]))
        // Uncomment the following line to preserve selection between presentations
        // self.clearsSelectionOnViewWillAppear = false

        // Uncomment the following line to display an Edit button in the navigation bar for this view controller.
        // self.navigationItem.rightBarButtonItem = self.editButtonItem()
    }
    
    override func viewDidAppear(animated: Bool) {
        var tabBarController = self.tabBarController as! TabBarViewController
        tabBarController.lastScreen = 0
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }

    // MARK: - Table view data source

    override func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        // #warning Potentially incomplete method implementation.
        // Return the number of sections.
        return 2
    }

    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete method implementation.
        // Return the number of rows in the section.
        if section == 0 {
            return self.offers.count
        } else if section == 1 {
            return self.requests.count
        }
        return 0
    }

    
    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("ExchangeCell", forIndexPath: indexPath) as! UITableViewCell
        if indexPath.section == 0 {
            var offer = offers[indexPath.row]
        
            // Set title as the club and number, and subtitle as the date
            cell.textLabel!.text = offer.club + " (" + String(offer.number) + ")"
            cell.detailTextLabel!.text = offer.date
        } else if indexPath.section == 1 {
            var request = requests[indexPath.row]
            
            // Set title as the club and number, and subtitle as the date
            cell.textLabel!.text = request.club + " (" + String(request.number) + ")"
            cell.detailTextLabel!.text = request.date
            cell.accessoryType = UITableViewCellAccessoryType.None
            
            let acceptButton = UIButton.buttonWithType(UIButtonType.System) as! UIButton
            acceptButton.titleLabel!.text = "Accept"
            cell.addSubview(acceptButton)
            
        }

        // Configure the cell...

        return cell
    }

    
    /***** popover for creating an exchange ****/
    
    // create exchange button pressed on popup
    @IBAction func addExchangePopup(segue:UIStoryboardSegue)
    {
        var clubString = popoverViewController.clubField.text
        var dateString = popoverViewController.dateField.text
        var numPassesString = popoverViewController.numPassesField.text
        
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
        
        let url = NSURL(string: exchangeString)
        
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            //println(NSString(data: data, encoding: NSUTF8StringEncoding))
            
            dispatch_async(dispatch_get_main_queue()) {
                
            }
        }
        task.resume()
        self.dismissViewControllerAnimated(true, completion: nil)
    }

    
    
    
    // specifics to happen when you call a segue
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        if segue.identifier == "popoverSegueAddExchange" {
            popoverViewController = segue.destinationViewController as! PopupForAddExchangeViewController
            popoverViewController.modalPresentationStyle = UIModalPresentationStyle.Popover
            popoverViewController.popoverPresentationController!.delegate = self
        }
    }
    
    // has to be a popover; otherwise unaccepted
    func adaptivePresentationStyleForPresentationController(controller: UIPresentationController) -> UIModalPresentationStyle {
        return UIModalPresentationStyle.None
    }

    /*
    // Override to support conditional editing of the table view.
    override func tableView(tableView: UITableView, canEditRowAtIndexPath indexPath: NSIndexPath) -> Bool {
        // Return NO if you do not want the specified item to be editable.
        return true
    }
    */

    /*
    // Override to support editing the table view.
    override func tableView(tableView: UITableView, commitEditingStyle editingStyle: UITableViewCellEditingStyle, forRowAtIndexPath indexPath: NSIndexPath) {
        if editingStyle == .Delete {
            // Delete the row from the data source
            tableView.deleteRowsAtIndexPaths([indexPath], withRowAnimation: .Fade)
        } else if editingStyle == .Insert {
            // Create a new instance of the appropriate class, insert it into the array, and add a new row to the table view
        }    
    }
    */

    /*
    // Override to support rearranging the table view.
    override func tableView(tableView: UITableView, moveRowAtIndexPath fromIndexPath: NSIndexPath, toIndexPath: NSIndexPath) {

    }
    */

    /*
    // Override to support conditional rearranging of the table view.
    override func tableView(tableView: UITableView, canMoveRowAtIndexPath indexPath: NSIndexPath) -> Bool {
        // Return NO if you do not want the item to be re-orderable.
        return true
    }
    */

    /*
    // MARK: - Navigation

    // In a storyboard-based application, you will often want to do a little preparation before navigation
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        // Get the new view controller using [segue destinationViewController].
        // Pass the selected object to the new view controller.
    }
    */

}
