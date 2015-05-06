//
//  LoginViewController.swift
//  P4P
//
//  Created by Daniel Yang on 4/7/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit
import SwiftyJSON

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
        let username: String = self.usernameTextField.text
        let password: String = self.passwordTextField.text
        let deviceID: String = "0f744707bebcf74f9b7c25d48e3358945f6aa01da5ddb387462c7eaf61bbad78"
        let pwHash: String = password.MD5()
        let url = NSURL(string: "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/mobileLogin.php?un=" + username + "&pwHash=" + pwHash + "&deviceID=" + deviceID)

        var loginViewController = self;
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            if let authResult = json.array {
                if count(authResult) > 0 {
                    dispatch_async(dispatch_get_main_queue()) {
                        self.parentViewController!.dismissViewControllerAnimated(true, completion: {
                            (UIApplication.sharedApplication().delegate as! AppDelegate).userNetid = username
                            (UIApplication.sharedApplication().delegate as! AppDelegate).pwHash = pwHash
                                self.delegate.completeLogin()
                            if let firstName = json["firstName"].string {
                                (UIApplication.sharedApplication().delegate as! AppDelegate).firstName = firstName
                            }
                            if let lastName = json["lastName"].string {
                                (UIApplication.sharedApplication().delegate as! AppDelegate).lastName = lastName
                            }
                            
                        });
                    }
                } else {
                    dispatch_async(dispatch_get_main_queue()) {
                        self.parentViewController!.dismissViewControllerAnimated(true, completion: nil)
                    }
                }
            } else {
                dispatch_async(dispatch_get_main_queue()) {
                    self.parentViewController!.dismissViewControllerAnimated(true, completion: nil)
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

extension Int {
    func hexString() -> String {
        return NSString(format:"%02x", self) as String
    }
}

extension NSData {
    func hexString() -> String {
        var string = String()
        for i in UnsafeBufferPointer<UInt8>(start: UnsafeMutablePointer<UInt8>(bytes), count: length) {
            string += Int(i).hexString()
        }
        return string
    }
    
    func MD5() -> NSData {
        let result = NSMutableData(length: Int(CC_MD5_DIGEST_LENGTH))!
        CC_MD5(bytes, CC_LONG(length), UnsafeMutablePointer<UInt8>(result.mutableBytes))
        return NSData(data: result)
    }
}

extension String {
    func MD5() -> String {
        return (self as NSString).dataUsingEncoding(NSUTF8StringEncoding)!.MD5().hexString()
    }
}
