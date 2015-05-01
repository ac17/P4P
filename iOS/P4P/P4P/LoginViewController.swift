//
//  LoginViewController.swift
//  P4P
//
//  Created by Daniel Yang on 4/7/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

protocol LoginViewControllerDelegate : NSObjectProtocol {
    func completeLogin()  // Segues to screen
}

class LoginViewController: UIViewController, UITextFieldDelegate {

    @IBOutlet weak var usernameTextField: UITextField!
    @IBOutlet weak var passwordTextField: UITextField!
    @IBOutlet weak var loginButton: UIButton!
    var delegate: LoginViewControllerDelegate! = nil
    
    override func viewDidLoad() {
        super.viewDidLoad()

        // Do any additional setup after loading the view.
        self.usernameTextField.delegate = self
        self.passwordTextField.delegate = self
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    // dismisses iOS keyboard after you open a textfield and touch anywhere else
    override func touchesBegan(touches: Set<NSObject>, withEvent event: UIEvent) {
        view.endEditing(true)
        super.touchesBegan(touches, withEvent: event)
    }
    
    // called when you hit enter in a text field. dismisses keyboard
    func textFieldShouldReturn(textField: UITextField) -> Bool {
        view.endEditing(true)
        return false
    }
    
    @IBAction func login(sender: AnyObject) {
        let url = NSURL(string: "http://www.stackoverflow.com")
        let username = self.usernameTextField.text
        let password = self.passwordTextField.text
        
        var validated = false
        var loginViewController = self;
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            println(NSString(data: data, encoding: NSUTF8StringEncoding))
            validated = true
            println(validated)
            if validated {
                dispatch_async(dispatch_get_main_queue()) {
                    self.parentViewController!.dismissViewControllerAnimated(true, completion: {
                        (UIApplication.sharedApplication().delegate as! AppDelegate).userNetid = username
                        self.delegate.completeLogin()
                    });
                }

            }
        }
        task.resume()
        

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
