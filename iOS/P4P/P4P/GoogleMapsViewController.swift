//
//  GoogleMapsViewController.swift
//  P4P
//
//  Created by Daniel Yang on 4/7/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit
import SwiftyJSON

class GoogleMapsViewController: UIViewController, CLLocationManagerDelegate, GMSMapViewDelegate, UIPopoverPresentationControllerDelegate {

    @IBOutlet var mapView: GMSMapView!
    let locationManager = CLLocationManager()
    var popoverViewController: PopupViewController!

    override func viewDidLoad() {
        super.viewDidLoad()
        // request access to user location
        locationManager.delegate = self
        locationManager.requestAlwaysAuthorization()
        if CLLocationManager.authorizationStatus() == .AuthorizedAlways {
            locationManager.startUpdatingLocation()
            mapView.myLocationEnabled = true
            mapView.settings.myLocationButton = true
        }
        self.view .insertSubview(mapView, atIndex:0)
        mapView.delegate = self
    }
    
    // function called when authorization revoked or granted
    func locationManager(manager: CLLocationManager!, didChangeAuthorizationStatus status: CLAuthorizationStatus) {
        if status == .AuthorizedAlways {
            locationManager.startUpdatingLocation()
            mapView.myLocationEnabled = true
            mapView.settings.myLocationButton = true
        }
    }
    
    // function called when new location data received
    func locationManager(manager: CLLocationManager!, didUpdateLocations locations: [AnyObject]!) {
        if let location = locations.first as? CLLocation {
            mapView.camera = GMSCameraPosition(target: location.coordinate, zoom: 15, bearing: 0, viewingAngle: 0)
            locationManager.stopUpdatingLocation()
        }
    }
    
    // popover segue - make it happen
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        if segue.identifier == "popoverSegue" {
            popoverViewController = segue.destinationViewController as! PopupViewController
            popoverViewController.modalPresentationStyle = UIModalPresentationStyle.Popover
            popoverViewController.popoverPresentationController!.delegate = self
        }
    }
    
    // has to be a popover; otherwise unaccepted
    func adaptivePresentationStyleForPresentationController(controller: UIPresentationController) -> UIModalPresentationStyle {
        return UIModalPresentationStyle.None
    }


    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
    
    func mapView(mapView: GMSMapView!, markerInfoWindow marker: GMSMarker!) -> UIView! {
        var infoWindow = NSBundle.mainBundle().loadNibNamed("CustomInfoWindow", owner: self, options: nil).first! as! CustomInfoWindow
        infoWindow.nameMarker.text = "\(marker.title) \(marker.position.latitude) \(marker.position.longitude)"
        return infoWindow
    }
    
    @IBAction func searchPassFilter(segue:UIStoryboardSegue)
    {
        mapView.clear()
        
        var clubString = popoverViewController.clubField.text
        var dateString = popoverViewController.dateField.text
        var numPassesString = popoverViewController.numPassesField.text
        
        // HTTP requests need format xx/yy/zz, not x/y/zz
        var formattedDateString = ""
        if !dateString.isEmpty {
            var dateStringArray = dateString.componentsSeparatedByString("/")
            if (count(dateStringArray[0]) == 1) {
                dateStringArray[0] = "0" + dateStringArray[0]
            }
            if (count(dateStringArray[1]) == 1) {
                dateStringArray[1] = "0" + dateStringArray[1]
            }
            if (count(dateStringArray[2]) == 2) {
                dateStringArray[2] = "20" + dateStringArray[2]
            }

            formattedDateString = dateStringArray[0] + "/" + dateStringArray[1] + "/" + dateStringArray[2]
        }
        
        // replace spaces in club name with pluses
        clubString = clubString.stringByReplacingOccurrencesOfString(" ", withString: "+", options: NSStringCompareOptions.LiteralSearch, range: nil)
        
        var requestString = "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/php/searchExchanges.php?"
        requestString += "date=" + formattedDateString + "&type=Offer" + "&numPasses=" + numPassesString + "&club=" + clubString
        //println(requestString)
        
        // pull info from server, display markers
        let url = NSURL(string: requestString)
        
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            //println(NSString(data: data, encoding: NSUTF8StringEncoding))
            let json = JSON(data: data)
            for (user: String, subJson: JSON) in json["Users"] {
                //println(subJson["netId"])
                //println(subJson["name"])
                //println(subJson["exchanges"])

                var name = "Bob"
                var latitude = "-33.86"
                var longitude = "151.20"

                if let temp = subJson["name"].string { name = temp }
                if let temp = subJson["lat"].string { latitude = temp }
                if let temp = subJson["lng"].string { longitude = temp }
                
                var passClubs = [String]()
                var passNumbers = [String]()
                var passComments = [String]()
              
                for(exchange: String, subsubJson: JSON) in subJson["exchanges"] {
                    var club = "Princeton"
                    var number = "12345"
                    var comment = "hi, test comment"
                    
                    if let temp = subsubJson["club"].string { club = temp }
                    if let temp = subsubJson["passNum"].string { number = temp }
                    if let temp = subsubJson["comment"].string { comment = temp }

                    passClubs.append(club)
                    passNumbers.append(number)
                    passComments.append(comment)
                }
                
                //println(passClubs)
                //println(passNumbers)
                //println(passComments)
                
                dispatch_async(dispatch_get_main_queue()) {
                    var marker = GMSMarker()
                    marker.position = CLLocationCoordinate2DMake((latitude as NSString).doubleValue, (longitude as NSString).doubleValue)
                    marker.title = name
                    var snippetString = ""
                    var index = 0
                    for exchangeString in passClubs {
                        snippetString += passClubs[index] + "-" + passNumbers[index] + "\n"
                        index++
                    }
                    marker.snippet = snippetString
                    marker.map = self.mapView
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
