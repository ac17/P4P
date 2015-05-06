//
//  PopupView.swift
//  P4P
//
//  Created by Frank Jiang on 5/5/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class PopupView: UIView {

    var popupViewController: PopupViewController?
    var popupForAddExchangeViewController: PopupForAddExchangeViewController?
    
    
    /*
    // Only override drawRect: if you perform custom drawing.
    // An empty implementation adversely affects performance during animation.
    override func drawRect(rect: CGRect) {
        // Drawing code
    }
    */
    
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
