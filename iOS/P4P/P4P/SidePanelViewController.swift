//
//  SidePanelViewController.swift
//  P4P
//
//  Created by Frank Jiang on 29/4/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class SidePanelViewController: UITableViewController {
    
    var users:[String] = ["ffjiang", "dan", "vibhaa", "ac17", "arturf"]
    var convos:[String: String] = ["ffjiang": "FRANK SAYS HI", "dan": "DAN SAYS HI", "vibhaa": "VIBHAA SAYS HI", "ac17": "ANGELICA SAYS HI", "arturf": "ARTUR SAYS HI"]
    
    var fillerView: UIView!
    
    override func viewDidLoad() {
        super.viewDidLoad()
        let tableView = self.view as! UITableView
        tableView.registerClass(ChatTableViewCell.self, forCellReuseIdentifier: "userCell")
        
        // Make side panel - move to left and down, and make thinner
        self.view.frame.size.width = self.view.frame.size.width - 250
        self.view.frame.origin.x = -self.view.frame.size.width
        self.view.frame.origin.y = 65
        
        // Allow selections
        (self.view as! UITableView).allowsSelection = true
        
        fillerView = UIView(frame: CGRectMake(self.view.frame.origin.x, 800.0, self.view.frame.size.width, 100.0))//UIScreen.mainScreen().bounds.size.height - self.view.frame.origin.y - self.view.frame.size.height))
        fillerView.backgroundColor = UIColor.grayColor()
        println(self.view.frame.origin.y + self.view.frame.size.height)
        println(self.view.frame.origin)
        println(self.view.frame.size)
        println(fillerView.frame.origin)
        println(fillerView.frame.size)
        self.view.insertSubview(fillerView, atIndex: 100)
        
        // Cache conversations
        for (user, conv) in convos {
            let url = NSURL(string: "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/php/chatRetrieveJSON.php?recipient=" + user + "&user=" + (UIApplication.sharedApplication().delegate as! AppDelegate).userNetid)
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                self.convos[user] = NSString(data: data, encoding: NSUTF8StringEncoding)! as String
            }
            task.resume()
        }
    }

    override func viewWillAppear(animated: Bool) {
        println("swift")
        // Set the currently selected user as the first user if one is not already chosen
        if let currentUser = (self.parentViewController as! ChatViewController).sidePanelCurrentlySelectedUser {
            if !contains(users, currentUser) {
                (self.parentViewController as! ChatViewController).sidePanelCurrentlySelectedUser = users[0]
                (self.view as! UITableView).selectRowAtIndexPath(NSIndexPath(indexes: [0, 0], length: 2), animated: false, scrollPosition: UITableViewScrollPosition.None)
            }
        } else {
            (self.parentViewController as! ChatViewController).sidePanelCurrentlySelectedUser = users[0]
            (self.view as! UITableView).selectRowAtIndexPath(NSIndexPath(indexes: [0, 0], length: 2), animated: false, scrollPosition: UITableViewScrollPosition.None)
        }
    }
    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    override func viewDidAppear(animated: Bool) {
        self.view.frame.size.height = (self.view as! UITableView).contentSize.height
        fillerView.frame.origin.y = self.view.frame.origin.y + self.view.frame.size.height
        fillerView.frame.size.height = UIScreen.mainScreen().bounds.size.height - self.view.frame.origin.y - self.view.frame.size.height
    }
    
    override func tableView(tableView: UITableView, numberOfRowsInSection section: Int) -> Int {
        return users.count
    }
    
    override func tableView(tableView: UITableView, cellForRowAtIndexPath indexPath: NSIndexPath) -> UITableViewCell {
        let cell = tableView.dequeueReusableCellWithIdentifier("userCell", forIndexPath: indexPath) as! UITableViewCell
        cell.textLabel?.text = users[indexPath.row]
        cell.contentView.backgroundColor = UIColor.darkGrayColor()
        cell.textLabel!.backgroundColor = UIColor.darkGrayColor()
        cell.textLabel!.textColor = UIColor.whiteColor()
        
        let tapRec = UITapGestureRecognizer(target: self.parentViewController!, action: "loadConversation:")
        cell.addGestureRecognizer(tapRec)
        
        
        return cell
    }
    
    override func tableView(tableView: UITableView, didSelectRowAtIndexPath indexPath: NSIndexPath) {
        let user: String = tableView.cellForRowAtIndexPath(indexPath)!.textLabel!.text!
        (self.parentViewController as! ChatViewController).chatTextView.text = convos[user]
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