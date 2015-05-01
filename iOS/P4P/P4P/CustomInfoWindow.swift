//
//  CustomInfoWindow.swift
//  P4P
//
//  Created by Daniel Yang on 4/28/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class CustomInfoWindow: UIView {//UITableViewDataSource, UITableViewDelegate {

    @IBOutlet weak var nameMarker: UILabel!
    @IBOutlet weak var selectorMarker: UISegmentedControl!
    @IBOutlet weak var exchangeMarker: UITableView!
    @IBAction func buttonTest(sender: AnyObject) {
        println("button pressed!")
    }
    
    //var mapInfoExchangeArray: [String] = []
    /*
    func numberOfSectionsInTableView(tableView: UITableView) -> Int {
        // #warning Potentially incomplete method implementation.
        // Return the number of sections.
        exchangeMarker.registerClass(UITableViewCell.self, forCellReuseIdentifier: "MarkerOfferCells")
        return 1
    }
    
    func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        // #warning Incomplete method implementation.
        // Return the number of rows in the section.
        return mapInfoExchangeArray.count
    }

    func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("MarkerOfferCells", forIndexPath: indexPath) as! UITableViewCell
        var offer = mapInfoExchangeArray[indexPath.row]
        cell.textLabel!.text =  offer
        
        // Configure the cell...
        return cell
    }
*/
    /*
    // Only override drawRect: if you perform custom drawing.
    // An empty implementation adversely affects performance during animation.
    override func drawRect(rect: CGRect) {
        // Drawing code
    }
    */

}
