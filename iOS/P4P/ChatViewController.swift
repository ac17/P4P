//
//  ChatViewController.swift
//  
//
//  Created by Frank Jiang on 28/4/15.
//
//

import UIKit

enum SlideOutState {
    case Normal
    case LeftPanelExpanded
}

class ChatViewController: UIViewController, UITextViewDelegate, UIGestureRecognizerDelegate {

    var textEntry: UITextView!
    var chatTextView: UITextView!
    var sendButton: UIButton!
    var toggleSidePanelButton: UIButton!
    
    var sidePanelCurrentlySelectedUser: String?
    
    var currentState: SlideOutState = .Normal {
        didSet {
            let shouldShowShadow = currentState != .Normal
            showShadowForMainViewController(shouldShowShadow)
        }
    }
    var leftViewController: SidePanelViewController?
    
    var mainPanelExpandedOffset: CGFloat = 250
    
    var openSidePanelRecognizer: UISwipeGestureRecognizer!
    var closeSidePanelRecognizer: UISwipeGestureRecognizer!
    
    override func viewDidLoad() {
        super.viewDidLoad()
    
        (self.view as! ChatView).viewController = self
        
        self.automaticallyAdjustsScrollViewInsets = false
        
        textEntry = UITextView()
        textEntry.delegate = self
        textEntry.becomeFirstResponder()
        textEntry.scrollEnabled = false
        textEntry.frame = CGRectMake(16.0, 400.0, 300.0, 30.0)
        textEntry.layer.borderWidth = 0.5
        textEntry.layer.borderColor = UIColor.grayColor().CGColor
        textEntry.layer.cornerRadius = 5.0
        self.view.addSubview(textEntry)
        
        chatTextView = UITextView()
        chatTextView.delegate = self
        chatTextView.frame = CGRectMake(16.0, 95.0, 340.0, 300.0)
        chatTextView.layer.borderWidth = 1.0
        chatTextView.layer.borderColor = UIColor.grayColor().CGColor
        chatTextView.layer.cornerRadius = 5.0
        chatTextView.editable = false
        self.view.addSubview(chatTextView)

        sendButton = UIButton.buttonWithType(UIButtonType.System) as! UIButton
        sendButton.setTitle("Send", forState: UIControlState.Normal)
        sendButton.frame = CGRectMake(325.0, 400.0, 40.0, 30.0)
        sendButton.addTarget(self, action: "sendMessage:", forControlEvents: UIControlEvents.TouchUpInside)
        sendButton.enabled = false
        self.view.addSubview(sendButton)
        
        toggleSidePanelButton = UIButton.buttonWithType(UIButtonType.Custom) as! UIButton
        toggleSidePanelButton.frame = CGRectMake(5, 65, 30, 30)
        toggleSidePanelButton.setImage(UIImage(named: "menuicon.png"), forState: .Normal)
        toggleSidePanelButton.addTarget(self, action: "toggleLeftPanel:", forControlEvents:.TouchUpInside)
        self.view.addSubview(toggleSidePanelButton)
        
        openSidePanelRecognizer = UISwipeGestureRecognizer(target: self, action: "openLeftPanel:")
        self.view.addGestureRecognizer(openSidePanelRecognizer)
        
        closeSidePanelRecognizer = UISwipeGestureRecognizer(target: self, action: "closeLeftPanel:")
        closeSidePanelRecognizer.direction = UISwipeGestureRecognizerDirection.Left
        self.view.addGestureRecognizer(closeSidePanelRecognizer)
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func sendMessage(sender: AnyObject) {
        if count(chatTextView.text) > 0 {
            chatTextView.text = chatTextView.text + "\n" + textEntry.text
        } else {
            chatTextView.text = textEntry.text
        }
        textEntry.text = ""
        
        textEntry.frame = CGRectMake(16.0, 400.0, 300.0, 30.0)
        chatTextView.frame = CGRectMake(16.0, 80.0, 343.0, 300.0)
        chatTextView.scrollRangeToVisible(NSMakeRange(count(chatTextView.text) - 1, 0))
    }

    // Adjusts the size of the two text views (the place where the user enters text, 
    // and the place showing the conversation), as the user enters text.
    func textViewDidChange(textView: UITextView) {
        var correctSize = textView.sizeThatFits(CGSizeMake(textView.frame.size.width, CGFloat.max))
        var heightDiff = correctSize.height - textEntry.frame.size.height
        textEntry.frame.size.height = correctSize.height
        textEntry.frame.origin.y = textEntry.frame.origin.y - heightDiff
        
        chatTextView.frame.size.height = chatTextView.frame.size.height - heightDiff
        chatTextView.scrollRangeToVisible(NSMakeRange(count(chatTextView.text) - 1, 0))
        
        if count(textView.text) > 0 {
            sendButton.enabled = true
        } else {
            sendButton.enabled = false
        }
    }
    
    // Toggles whether the left side panel with the list of users that you are
    // cahtting with is expanded or not
    func openLeftPanel(gesture: UIGestureRecognizer) {
        if currentState == .Normal && gesture.locationOfTouch(0, inView: self.view).x < 100 {
            addLeftPanelViewController()
            animateLeftPanel(shouldExpand: true)
        }
    }
        
    func closeLeftPanel(gesture: UIGestureRecognizer) {
        if currentState == .LeftPanelExpanded {
            animateLeftPanel(shouldExpand: false)
        }
    }
    
    func toggleLeftPanel(sender: AnyObject) {
        if currentState == .Normal {
            addLeftPanelViewController()
            animateLeftPanel(shouldExpand: true)
        } else {
            animateLeftPanel(shouldExpand: false)
        }
    }
    
    func addLeftPanelViewController() {
        if leftViewController == nil {
            leftViewController = SidePanelViewController()
        }
        self.addChildViewController(leftViewController!)
        leftViewController?.didMoveToParentViewController(self)
        self.view.addSubview(leftViewController!.view)
    }
    
    func animateLeftPanel(#shouldExpand: Bool) {
        if shouldExpand {
            currentState = .LeftPanelExpanded
            
            // Animate the main panel
            animateMainPanelXPosition(targetPosition: CGRectGetWidth(self.view.frame) - mainPanelExpandedOffset)
        } else {
            animateMainPanelXPosition(targetPosition: 0) { finished in
                self.currentState = .Normal
                self.leftViewController!.view.removeFromSuperview()
                self.leftViewController = nil
            }
        }
    }
    
    func animateMainPanelXPosition(#targetPosition: CGFloat, completion: ((Bool) -> Void)! = nil) {
        UIView.animateWithDuration(0.5, delay: 0, usingSpringWithDamping: 0.8, initialSpringVelocity: 0, options: .CurveEaseInOut, animations: {
            self.view.frame.origin.x = targetPosition
        }, completion: completion)
    }
    
    @IBAction func leaveChatScreen() {
        var tabBarController = self.tabBarController as! TabBarViewController
        self.tabBarController!.selectedIndex = tabBarController.lastScreen
    }
    
    func loadConversation(sender: UITapGestureRecognizer) {
        println("hi")
        if sender.state == .Ended {
            let user: String = (sender.view as! UITableViewCell).textLabel!.text!
            chatTextView.text = leftViewController!.convos[user]
        }
    }
    
    func showShadowForMainViewController(shouldShowShadow: Bool) {
        if shouldShowShadow {
            self.view.layer.shadowOpacity = 0.8
        } else {
            self.view.layer.shadowOpacity = 0.0
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
