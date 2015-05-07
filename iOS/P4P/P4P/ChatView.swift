//
//  ChatView.swift
//  P4P
//
//  Created by Frank Jiang on 4/5/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class ChatView: UIScrollView {

    var viewController: ChatViewController?
    var i = 1
    /*
    // Only override drawRect: if you perform custom drawing.
    // An empty implementation adversely affects performance during animation.
    override func drawRect(rect: CGRect) {
        // Drawing code
    }
    */
    
    // Ensure that the side panel can receive touches
    override func hitTest(point: CGPoint, withEvent event: UIEvent?) -> UIView? {
        if viewController!.currentState == .LeftPanelExpanded {
            let sidePanelFrame = viewController!.leftViewController!.view.frame
            if CGRectContainsPoint(sidePanelFrame, point) {
                return viewController!.leftViewController!.view
            }
        }
        
        return super.hitTest(point, withEvent: event)
    }

}
