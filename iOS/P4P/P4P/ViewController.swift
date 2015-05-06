//
//  ViewController.swift
//  P4P
//
//  Created by Frank Jiang on 6/4/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class ViewController: UIViewController, LoginViewControllerDelegate {
    //var tabBarController;
    
    var backgroundView: UIImageView?
    
    var userValidated = false  // Whether the user's username and password were authenticated.
    
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view, typically from a nib.
        userValidated = true   // Replace this with login logic
        
        backgroundView = UIImageView(image: UIImage(named: "HomeScreen.png"))
        backgroundView!.frame = UIScreen.mainScreen().bounds
        self.view.insertSubview(backgroundView!, atIndex: 0)
    }
    
    override func viewWillAppear(animated: Bool) {
        UIApplication.sharedApplication().statusBarStyle = .LightContent
    }
    
    override func viewWillDisappear(animated: Bool) {
        UIApplication.sharedApplication().statusBarStyle = .Default
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
    
    @IBAction func presentLoginScreen(sender: AnyObject) {
        performSegueWithIdentifier("presentLogin", sender: sender)
    }
    
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject!) {
        if segue.identifier == "presentLogin" {
            let viewControllers: NSArray = segue.destinationViewController.viewControllers
            let loginViewController1: LoginViewController = viewControllers[0] as! LoginViewController
            loginViewController1.delegate = self
        }
        if segue.identifier == "presentRegister" {
            let viewControllers: NSArray = segue.destinationViewController.viewControllers
            let registerViewController: RegisterViewController = viewControllers[0] as! RegisterViewController
            registerViewController.delegate = self
        }
        
    }
    
    func completeLogin() {
        performSegueWithIdentifier("openDash", sender: self)
    }

    
    
}

