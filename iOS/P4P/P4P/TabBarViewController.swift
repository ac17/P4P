//
//  TabBarViewController.swift
//  P4P
//
//  Created by Frank Jiang on 22/4/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class TabBarViewController: UITabBarController {

    var lastScreen = 1
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        (UIApplication.sharedApplication().delegate as! AppDelegate).tabBarController = self

        self.selectedIndex = 1
        // Do any additional setup after loading the view.
    }


    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
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
