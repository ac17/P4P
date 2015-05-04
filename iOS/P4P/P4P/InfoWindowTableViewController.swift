//
//  InfoWindowTableViewController.swift
//  P4P
//
//  Created by Daniel Yang on 5/1/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class InfoWindowTableViewController: UITableViewController {

    var mapInfoWindowNetID: String = ""
    var mapInfoWindowName: String = ""
    var mapInfoWindowNumberOffers: String = ""
    var mapInfoExchangeArray: [String] = []
    var mapInfoExchangeIDArray: [String] = []
    var appNetID = "arturf"
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view.
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
        
        // Configure the cell...
        return cell
    }
    
    // if you select a cell, make the request and change how the cell is displayed
    override func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath) {
        let cell = tableView.cellForRowAtIndexPath(indexPath)
        if cell!.accessoryType != UITableViewCellAccessoryType.Checkmark
        {
            cell!.accessoryType = UITableViewCellAccessoryType.Checkmark
            
            var requestString = "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/php/pursueOffer.php?"
            requestString += "netId=" + appNetID + "&offerId=" + mapInfoExchangeIDArray[indexPath.row]
            println(requestString)
            
            // pull info from server, display markers
            let url = NSURL(string: requestString)

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
