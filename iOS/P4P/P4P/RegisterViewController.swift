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
    
    var delegate: LoginViewControllerDelegate! = nil
    
    var imagePicker: UIImagePickerController!
    
    let tapRec = UITapGestureRecognizer()
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        self.view.backgroundColor = UIColor.clearColor()

        // Do any additional setup after loading the view.
        
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

    @IBAction func register(sender: AnyObject) {
        
        println("lololol")
        let netid = self.netIDTextField.text
        let firstName = self.firstNameTextField.text
        let lastName = self.lastNameTextField.text
        let password = self.passwordTextField.text
        let pwHash = password.MD5()
    
        
        let url = NSURL(string: "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/mobileRegistration.php?fName=" + firstName + "&lName=" + lastName +  "&netId=" + netid + "&pwHash=" + pwHash)
        

        var registerViewController = self;
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            if let authResult = json["regResults"].array {
                println(authResult[0])
                if authResult[0] == "TRUE" {
                    dispatch_async(dispatch_get_main_queue()) {
                        self.parentViewController!.dismissViewControllerAnimated(true, completion: {
                            (UIApplication.sharedApplication().delegate as! AppDelegate).userNetid = netid
                            (UIApplication.sharedApplication().delegate as! AppDelegate).pwHash = pwHash
                            self.delegate.completeLogin()
                        });
                    }
                } else {
                    dispatch_async(dispatch_get_main_queue()) {
                        self.parentViewController!.dismissViewControllerAnimated(true, completion: nil)
                    }
                    // INDICATE THAT IT FAILED TO REGISTER
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

