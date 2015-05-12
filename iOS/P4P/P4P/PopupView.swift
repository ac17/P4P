//
//  PopupView.swift
//  P4P
//
//  Created by Frank Jiang on 5/5/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//
//  popup view associated with the popupforaddexchange viewcontroller and popup view controller
//  needed so that default values could be given for the text field for the date pickers
//

import UIKit

class PopupView: UIView {

    // associated view controllers
    var popupViewController: PopupViewController?
    var popupForAddExchangeViewController: PopupForAddExchangeViewController?
    
    // make it so that the text field displays the date for the date after it's been selected
    override func hitTest(point: CGPoint, withEvent event: UIEvent?) -> UIView? {
        if let viewController = popupViewController {
            if CGRectContainsPoint(viewController.dateField!.frame, point) && viewController.dateField!.text == "" {
                var dateFormatter = NSDateFormatter()
                dateFormatter.dateStyle = NSDateFormatterStyle.ShortStyle
                var strDate = dateFormatter.stringFromDate(NSDate())
                viewController.dateField!.text = strDate
            }
        } else if let viewController = popupForAddExchangeViewController {
            if CGRectContainsPoint(viewController.dateField!.frame, point) && viewController.dateField!.text == "" {
                var dateFormatter = NSDateFormatter()
                dateFormatter.dateStyle = NSDateFormatterStyle.ShortStyle
                var strDate = dateFormatter.stringFromDate(NSDate())
                viewController.dateField!.text = strDate
            }
        }
        
        return super.hitTest(point, withEvent: event)
    }

}
