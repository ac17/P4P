//
//  GoogleMapsViewController.swift
//  P4P
//
//  Created by Daniel Yang on 4/7/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//
//  view controller for all things related to the maps tab
//  manages location tracking, displaying markers and allowing for interactions, 
//  links to many other view controllers with popups (filter exchanges) and infowindows (click on marker)
//

import UIKit
import SwiftyJSON

class GoogleMapsViewController: UIViewController, CLLocationManagerDelegate, GMSMapViewDelegate, UIPopoverPresentationControllerDelegate {

    
    @IBOutlet var mapView: GMSMapView!                              // the actual Google Maps View
    let locationManager = CLLocationManager()                       // necessary to track location data
    
    // global variables from app delegate
    var appNetID = ""
    var websiteURLbase = ""

    // other controllers referenced
    var popoverViewController: PopupViewController!                 // for filtering exchanges
    var infoWindowViewController: InfoWindowTableViewController!    // for seeing offers and making requests
    var infoWindowNavigationController: UINavigationController!     // necessary to jump to Chat tab

    // global variables used to pass information to the InfoWindow that appears when you click on a marker
    var mapInfoWindowNetID: String = ""
    var mapInfoWindowName: String = ""
    var mapInfoWindowNumberOffers: String = ""
    var mapInfoExchangeArray: [String] = []
    var mapInfoExchangeIDArray: [String] = []
    
    override func viewDidLoad() {
        super.viewDidLoad()
        
        // request access to user location and begin updating
        locationManager.delegate = self
        locationManager.requestAlwaysAuthorization()
        if CLLocationManager.authorizationStatus() == .AuthorizedAlways {
            locationManager.startUpdatingLocation()
            mapView.myLocationEnabled = true
            mapView.settings.myLocationButton = true
        }
        mapView.delegate = self
        
        // pull global values from the app delegate
        let appDelegate = UIApplication.sharedApplication().delegate as! AppDelegate
        appNetID = appDelegate.userNetid
        websiteURLbase = appDelegate.websiteURLBase
    }
    
    override func viewDidAppear(animated: Bool) {
        var tabBarController = self.tabBarController as! TabBarViewController
        tabBarController.lastScreen = 1
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
            // move camera to current location
            mapView.camera = GMSCameraPosition(target: location.coordinate, zoom: 15, bearing: 0, viewingAngle: 0)
            
            // update location on database
            var updateLocationString = self.websiteURLbase + "/php/updateLocation.php?"
            var latitude = String(stringInterpolationSegment: location.coordinate.latitude)
            var longitude = String(stringInterpolationSegment: location.coordinate.longitude)
            updateLocationString += "currentUserNetId=" + appNetID + "&lat=" + latitude + "&lng=" + longitude
            
            let url = NSURL(string: updateLocationString)
            
            let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
                dispatch_async(dispatch_get_main_queue()) {
                }
            }
            task.resume()

            locationManager.stopUpdatingLocation()
        }
    }
    
    // function called when my location button tapped
    func didTapMyLocationButtonForMapView(mapView: GMSMapView!) -> Bool {
        locationManager.startUpdatingLocation()
        return false
    }

    // specifics to happen when you call a segue
    override func prepareForSegue(segue: UIStoryboardSegue, sender: AnyObject?) {
        // preparation necessary for a popover window to appear
        if segue.identifier == "popoverSegue" {
            popoverViewController = segue.destinationViewController as! PopupViewController
            popoverViewController.modalPresentationStyle = UIModalPresentationStyle.Popover
            popoverViewController.popoverPresentationController!.delegate = self
        }
        
        // pass global variables to the InfoWindowController and clear array's for future use in this view controller
        if segue.identifier == "infoWindowModal" {
            infoWindowNavigationController = segue.destinationViewController as! UINavigationController
            infoWindowViewController = infoWindowNavigationController.topViewController as! InfoWindowTableViewController
            infoWindowViewController.mapInfoWindowNetID = mapInfoWindowNetID
            infoWindowViewController.mapInfoWindowName = mapInfoWindowName
            infoWindowViewController.mapInfoWindowNumberOffers = mapInfoWindowNumberOffers
            infoWindowViewController.mapInfoExchangeArray = mapInfoExchangeArray
            infoWindowViewController.mapInfoExchangeIDArray = mapInfoExchangeIDArray
            
            infoWindowViewController.title = mapInfoWindowName
            
            mapInfoExchangeArray.removeAll()
            mapInfoExchangeIDArray.removeAll()
        }
    }
    
    // has to be a popover; otherwise unaccepted
    func adaptivePresentationStyleForPresentationController(controller: UIPresentationController) -> UIModalPresentationStyle {
        return UIModalPresentationStyle.None
    }

    
    // when you click on a a marker, create a custom InfoWindow instead of Google default window
    func mapView(mapView: GMSMapView!, markerInfoWindow marker: GMSMarker!) -> UIView! {
        // parse information saved in the marker when initially generated
        mapInfoWindowNetID = marker.title
        mapInfoWindowName = marker.snippet.componentsSeparatedByString("-")[0]
        mapInfoWindowNumberOffers = marker.snippet.componentsSeparatedByString("-")[1]
        var mapInfoWindowExchanges = marker.snippet.componentsSeparatedByString("-")[2]
        var mapInfoExchangeArrayPre = mapInfoWindowExchanges.componentsSeparatedByString(",")
        for exchangeInfo in mapInfoExchangeArrayPre {
            mapInfoExchangeIDArray.append(exchangeInfo.componentsSeparatedByString("!")[0])
            mapInfoExchangeArray.append(exchangeInfo.componentsSeparatedByString("!")[1])
        }

        performSegueWithIdentifier("infoWindowModal", sender: self)
        return UIView(frame: CGRectMake(0,0,0,0))
    }
    
    // filter button pressed on popup
    @IBAction func searchPassFilter(segue:UIStoryboardSegue)
    {
        mapView.clear()
        
        // extract parameters for filtering
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
        
        // generate HTTP request string with parameters
        var requestString = self.websiteURLbase + "/php/searchExchangesUserSpecific.php?"
        requestString += "netId=" + appNetID + "&date=" + formattedDateString + "&type=Offer" + "&numPasses=" + numPassesString + "&club=" + clubString
        
        // pull info from server, display markers
        let url = NSURL(string: requestString)
        
        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            let json = JSON(data: data)
            // parse through all the JSON coming your way
            for (user: String, subJson: JSON) in json["Users"] {
                var name = "Bob"
                var netID = "bobsmith"
                var latitude = "-33.86"
                var longitude = "151.20"

                // extract these fields to define the person and location
                if let temp = subJson["name"].string { name = temp }
                if let temp = subJson["netId"].string { netID = temp }
                if let temp = subJson["lat"].string { latitude = temp }
                if let temp = subJson["lng"].string { longitude = temp }
                
                // for every exchange, parse information into these arrays
                var passClubs = [String]()
                var passNumbers = [String]()
                var passComments = [String]()
                var passExchangeID = [String]()
              
                for(exchange: String, subsubJson: JSON) in subJson["exchanges"] {
                    var club = "Princeton"
                    var number = "12345"
                    var comment = "hi, test comment"
                    var exchangeID = "000"
                    
                    if let temp = subsubJson["club"].string { club = temp }
                    if let temp = subsubJson["passNum"].string { number = temp }
                    if let temp = subsubJson["comment"].string { comment = temp }
                    if let temp = subsubJson["id"].string { exchangeID = temp }

                    passClubs.append(club)
                    passNumbers.append(number)
                    passComments.append(comment)
                    passExchangeID.append(exchangeID)
                }
                
                // asynchonous call to generate the markers
                dispatch_async(dispatch_get_main_queue()) {
                    var marker = GMSMarker()
                    marker.position = CLLocationCoordinate2DMake((latitude as NSString).doubleValue, (longitude as NSString).doubleValue)
                    marker.title = netID
                    
                    // store lots of information into the marker snippet to be used later on; makes it easier instead
                    // of recalling data again; use symbols like "!", ",", " " and "-" to separate later on
                    var snippetString = ""
                    var index = 0
                    for exchangeString in passClubs {
                        snippetString += passExchangeID[index] + "!" + passClubs[index] + " " + passNumbers[index] + ","
                        index++
                    }

                    let snippetStringLength = count(snippetString)
                    let substringIndex = snippetStringLength - 1 // cut out the extra comma appended
                    snippetString = snippetString.substringToIndex(advance(snippetString.startIndex, substringIndex))
                    snippetString = name + "-" + String(index) + "-" + snippetString
                    
                    marker.snippet = snippetString
                    marker.map = self.mapView
                }
            }
        }
        task.resume()
    }
    
    // allow for returning to maps view controller
    @IBAction func returnToMap(segue:UIStoryboardSegue) {
    
    }

    // go to chat and chat with a specific person
    @IBAction func returnBeforeCallingChatFromMap (segue:UIStoryboardSegue) {
        self.tabBarController!.selectedIndex = 2
        for index in 0...2 {
            if let controller = self.tabBarController!.viewControllers![index] as? UINavigationController {
                if let chatController = controller.topViewController as? ChatViewController {
                    chatController.sidePanelCurrentlySelectedUser = mapInfoWindowNetID
                }
            }
        }
    }

    
    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }
}
