//
//  RegisterViewController.swift
//  P4P
//
//  Created by Daniel Yang on 4/7/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class RegisterViewController: UIViewController, UITextFieldDelegate, UINavigationControllerDelegate, UIImagePickerControllerDelegate {

    @IBOutlet weak var netIDTextField: UITextField!
    @IBOutlet weak var firstNameTextField: UITextField!
    @IBOutlet weak var lastNameTextField: UITextField!
    @IBOutlet weak var passwordTextField: UITextField!
    @IBOutlet weak var registerButton: UIButton!
    @IBOutlet weak var userPhotoView: UIImageView!
    
    var imagePicker: UIImagePickerController!
    
    let tapRec = UITapGestureRecognizer()
    
    override func viewDidLoad() {
        super.viewDidLoad()

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
    override func touchesBegan(touches: NSSet, withEvent event: UIEvent) {
        view.endEditing(true)
        super.touchesBegan(touches, withEvent: event)
    }
    
    // called when you hit enter in a text field. dismisses keyboard
    func textFieldShouldReturn(textField: UITextField!) -> Bool {
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
        var netid = self.netIDTextField.text
        var firstName = self.firstNameTextField.text
        var lastName = self.lastNameTextField.text
        var password = self.passwordTextField.text
        
        var validated = false
        
        let url = NSURL(string: "http://www.stackoverflow.com")
        

        var registerViewController = self;
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            println(NSString(data: data, encoding: NSUTF8StringEncoding))
            validated = true
            println(validated)
            if validated {
                dispatch_async(dispatch_get_main_queue()) {
                    registerViewController.performSegueWithIdentifier("loginToDash", sender: sender)
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
