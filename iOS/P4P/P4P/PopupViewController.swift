//
//  PopupViewController.swift
//  P4P
//
//  Created by Daniel Yang on 4/20/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit

class PopupViewController: UIViewController, UIPickerViewDataSource,UIPickerViewDelegate {

    @IBOutlet weak var clubField: UITextField!
    @IBOutlet weak var dateField: UITextField!
    @IBOutlet weak var numPassesField: UITextField!
    
    let pickerData = ["Ivy Club", "Tiger Inn", "Colonial", "Cottage", "Cap & Gown", "Tiger Inn", "All"]
    var clubWheel: UIPickerView!
    var datePickerView: UIDatePicker!

    
    override func viewDidLoad() {
        super.viewDidLoad()
        
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
        clubField.text = pickerData[clubWheel.selectedRowInComponent(0)]
    }
    
    //MARK: - Delegates and data sources
    //MARK: Data Sources
    func numberOfComponentsInPickerView(pickerView: UIPickerView) -> Int {
        return 1
    }
    func pickerView(pickerView: UIPickerView, numberOfRowsInComponent component: Int) -> Int {
        return pickerData.count
    }
    
    //MARK: Delegates
    func pickerView(pickerView: UIPickerView, titleForRow row: Int, forComponent component: Int) -> String! {
        return pickerData[row]
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
