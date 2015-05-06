//
//  RegisterViewController.swift
//  P4P
//
//  Created by Daniel Yang on 4/7/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit
import SwiftyJSON

class RegisterViewController: UIViewController, UITextFieldDelegate, UINavigationControllerDelegate, UIImagePickerControllerDelegate {

    @IBOutlet weak var netIDTextField: UITextField!
    @IBOutlet weak var firstNameTextField: UITextField!
    @IBOutlet weak var lastNameTextField: UITextField!
    @IBOutlet weak var passwordTextField: UITextField!
    @IBOutlet weak var registerButton: UIButton!
    @IBOutlet weak var userPhotoView: UIImageView!
    
    var imagePicker: UIImagePickerController!
    
    var backgroundView: UIImageView?
    
    let tapRec = UITapGestureRecognizer()
    
    @IBOutlet weak var failedRegisterLabel: UILabel!
    @IBOutlet weak var successRegisterLabel: UILabel!
    @IBOutlet weak var emptyFormsLabel: UILabel!
    
    override func viewDidLoad() {
        super.viewDidLoad()

        // Do any additional setup after loading the view.
        
        // Set background color to dark blue
        backgroundView = UIImageView(image: UIImage(named: "darkbluebackground.png"))
        backgroundView!.frame = UIScreen.mainScreen().bounds
                self.view.insertSubview(backgroundView!, atIndex: 0)
        
        // Make the image view interactive
        self.userPhotoView.userInteractionEnabled = true
        
        // Set up the gesture recognizer
        tapRec.addTarget(self, action: "takeSelfie")
        self.userPhotoView.addGestureRecognizer(tapRec)
        
        // part of dismissing keyboard
        self.netIDTextField.delegate = self
        self.firstNameTextField.delegate = self
        self.lastNameTextField.delegate = self
        self.passwordTextField.delegate = self
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
    
    // called when the image is tapped. opens the camera so the user can take a selfie
    func takeSelfie() {
        self.imagePicker = UIImagePickerController()
        self.imagePicker.delegate = self
        self.imagePicker.sourceType = .Camera
        
        presentViewController(imagePicker, animated: true, completion: nil)
    }
    
    func imagePickerController(picker: UIImagePickerController, didFinishPickingMediaWithInfo info: [NSObject : AnyObject]) {
        imagePicker.dismissViewControllerAnimated(true, completion: nil)
        self.userPhotoView.image = info[UIImagePickerControllerOriginalImage] as? UIImage
    }
    
    @IBAction func returnToHomeScreen(sender: AnyObject) {
        self.parentViewController!.dismissViewControllerAnimated(true, completion: nil)
    }

    @IBAction func register(sender: AnyObject) {
        if self.failedRegisterLabel.alpha != 0.0 {
            dispatch_async(dispatch_get_main_queue()) {
                UIView.animateWithDuration(0.1, delay: 0, options: UIViewAnimationOptions.CurveEaseOut, animations: {
                    self.failedRegisterLabel.alpha = 0.0
                    
                    }, completion: nil)
            }
        }
        if self.emptyFormsLabel.alpha != 0.0 {
            dispatch_async(dispatch_get_main_queue()) {
                UIView.animateWithDuration(0.1, delay: 0, options: UIViewAnimationOptions.CurveEaseOut, animations: {
                    self.emptyFormsLabel.alpha = 0.0
                    
                    }, completion: nil)
            }
        }
        
        let netid = self.netIDTextField.text
        let firstName = self.firstNameTextField.text
        let lastName = self.lastNameTextField.text
        let password = self.passwordTextField.text
        let pwHash = password.MD5()
        
        if netid == "" || firstName == "" || lastName == "" || password == "" {
            UIView.animateWithDuration(0.5, delay: 0, options: UIViewAnimationOptions.CurveEaseOut, animations: {
                self.emptyFormsLabel.alpha = 1.0
                }, completion: nil)
            return
        }
    
        
        let url = NSURL(string: "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/mobileRegistration.php?fName=" + firstName + "&lName=" + lastName +  "&netId=" + netid + "&pwHash=" + pwHash)
        

        var registerViewController = self;
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            if let authResult = json["regResults"].array {
                if authResult[0] == "TRUE" {
                    dispatch_async(dispatch_get_main_queue()) {
                        UIView.animateWithDuration(0.5, delay: 0, options: UIViewAnimationOptions.CurveEaseOut, animations: {
                            self.successRegisterLabel.alpha = 1.0
                            }, completion: nil)
                    }
                } else {
                    dispatch_async(dispatch_get_main_queue()) {
                        UIView.animateWithDuration(0.5, delay: 0, options: UIViewAnimationOptions.CurveEaseOut, animations: {
                            self.failedRegisterLabel.alpha = 1.0
                            }, completion: nil)
                    }
                }
            } else {
                dispatch_async(dispatch_get_main_queue()) {
                    UIView.animateWithDuration(0.5, delay: 0, options: UIViewAnimationOptions.CurveEaseOut, animations: {
                        self.failedRegisterLabel.alpha = 1.0
                        }, completion: nil)
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

extension UIColor {
    convenience init(red: Int, green: Int, blue: Int) {
        assert(red >= 0 && red <= 255, "Invalid red component")
        assert(green >= 0 && green <= 255, "Invalid green component")
        assert(blue >= 0 && blue <= 255, "Invalid blue component")
        
        self.init(red: CGFloat(red) / 255.0, green: CGFloat(green) / 255.0, blue: CGFloat(blue) / 255.0, alpha: 1.0)
    }
    
    convenience init(netHex:Int) {
        self.init(red:(netHex >> 16) & 0xff, green:(netHex >> 8) & 0xff, blue:netHex & 0xff)
    }
}

