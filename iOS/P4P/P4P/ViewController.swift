//
//  ViewController.swift
//  P4P
//
//  Created by Frank Jiang on 6/4/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class ViewController: UIViewController {
    //var tabBarController;
    
    var userValidated = false  // Whether the user's username and password were authenticated.
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view, typically from a nib.
        userValidated = true   // Replace this with login logic
        //self.tabBarController.selectedIndex = 1
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    @IBAction func openDash(sender: AnyObject) {
        if userValidated {
            performSegueWithIdentifier("openDash", sender: sender)
        }
    }

    @IBAction func cancelReturnToHome(segue:UIStoryboardSegue) {
        
    }

    
    
}

