//
//  PopupForAddExchangeViewController.swift
//  P4P
//
//  Created by Daniel Yang on 5/4/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class PopupForAddExchangeViewController: UIViewController, UIPickerViewDataSource,UIPickerViewDelegate {

    @IBOutlet weak var clubField: UITextField!
    @IBOutlet weak var dateField: UITextField!
    @IBOutlet weak var numPassesField: UITextField!
    
    let clubPickerData = ["Cannon", "Cap and Gown", "Charter", "Cloister", "Colonial", "Cottage", "Ivy Club", "Terrace", "Tiger Inn", "Tower"]
    var clubWheel: UIPickerView!
    var datePickerView: UIDatePicker!
    var numPassesWheel: UIPickerView!
    
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        (self.view as! PopupView).popupForAddExchangeViewController = self
        
        // make wheel of clubs and set textbox to use as input
        clubWheel = UIPickerView()
        clubWheel.dataSource = self
        clubWheel.delegate = self
        clubField.inputView = clubWheel
        
        // make a date picker and set textbox to use as input
        datePickerView = UIDatePicker()
        datePickerView.datePickerMode = UIDatePickerMode.Date
        dateField.inputView = datePickerView
        
        // whenever, the date is changed, call method "datePickerChanged"
        datePickerView.addTarget(self, action: Selector("datePickerChanged:"), forControlEvents: UIControlEvents.ValueChanged)
        
        // make wheel of numbers of and set numPassesField to use as input
        numPassesWheel = UIPickerView()
        numPassesWheel.dataSource = self
        numPassesWheel.delegate = self
        numPassesField.inputView = numPassesWheel
    }
    
    // whenever the date picker changed, do this
    func datePickerChanged(datePicker:UIDatePicker) {
        var dateFormatter = NSDateFormatter()
        
        dateFormatter.dateStyle = NSDateFormatterStyle.ShortStyle
        
        var strDate = dateFormatter.stringFromDate(datePicker.date)
        dateField.text = strDate
    }
    
    // when a row of picker view is selected, make text box have info
    func pickerView(pickerView: UIPickerView, didSelectRow row: Int, inComponent component: Int) {
        if pickerView === clubWheel {
            clubField.text = clubPickerData[pickerView.selectedRowInComponent(0)]
        } else if pickerView === numPassesWheel {
            numPassesField.text = String(pickerView.selectedRowInComponent(0) + 1)
        }
    }
    
    //MARK: - Delegates and data sources
    //MARK: Data Sources
    func numberOfComponentsInPickerView(pickerView: UIPickerView) -> Int {
        if pickerView === clubWheel && clubField.text == "" {
            clubField.text = "Cannon"
        } else if pickerView === numPassesWheel && numPassesField.text == "" {
            numPassesField.text = "1"
        }
        return 1
    }
    
    func pickerView(pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
        if pickerView === clubWheel {
            return clubPickerData.count
        } else if pickerView === numPassesWheel {
            return 99
        }
        return 0
    }
    
    //MARK: Delegates
    func pickerView(pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String! {
        if pickerView === clubWheel {
            return clubPickerData[row]
        } else if pickerView === numPassesWheel {
            return String(row + 1)
        }
        return ""
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
