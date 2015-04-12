//
//  GoogleMapsViewController.swift
//  P4P
//
//  Created by Daniel Yang on 4/7/15.
//  Copyright (c) 2015 P4P. All rights reserved.
//

import UIKit
import SwiftyJSON

class GoogleMapsViewController: UIViewController, CLLocationManagerDelegate {

    var mapView: GMSMapView!
    let locationManager = CLLocationManager()

    override func viewDidLoad() {
        super.viewDidLoad()

        // Do any additional setup after loading the view.
        var camera = GMSCameraPosition.cameraWithLatitude(40.348,
            longitude: -74.653, zoom: 17)
        mapView = GMSMapView.mapWithFrame(CGRectZero, camera: camera)
        self.view = mapView
        
        // request access to user location
        locationManager.delegate = self
        locationManager.requestAlwaysAuthorization()
        if CLLocationManager.authorizationStatus() == .AuthorizedAlways {
            locationManager.startUpdatingLocation()
            mapView.myLocationEnabled = true
            mapView.settings.myLocationButton = true
        }
        
        // padding - need to find a better way of doing this than hardcoding
        var mapInsets = UIEdgeInsetsMake(self.topLayoutGuide.length, 0.0, 50.0, 0.0)
        mapView.padding = mapInsets
        
        // pull info from server, display markers
        let url = NSURL(string: "http://ec2-54-149-32-72.us-west-2.compute.amazonaws.com/php/searchExchanges.php?date=04/05/2015&type=Offer&numPasses=1&club=Colonial")

        let task = NSURLSession.sharedSession().dataTaskWithURL(url!) {(data, response, error) in
            //println(NSString(data: data, encoding: NSUTF8StringEncoding))
            let json = JSON(data: data)
            var index = 0
            for exchange in json["Exchanges"] {
                var latitude = "-33.86"
                var longitude = "151.20"
                var name = "Bob"
                var club = "Princeton"
                var passes = "9"
                
                if let temp = json["Exchanges"][index]["lat"].string { latitude = temp }
                if let temp = json["Exchanges"][index]["lng"].string { longitude = temp }
                if let temp = json["Exchanges"][index]["name"].string { name = temp }
                if let temp = json["Exchanges"][index]["club"].string { club = temp }
                if let temp = json["Exchanges"][index]["passNum"].string { passes = temp }
                
                index++
                
                dispatch_async(dispatch_get_main_queue()) {
                    var marker = GMSMarker()
                    marker.position = CLLocationCoordinate2DMake((latitude as NSString).doubleValue, (longitude as NSString).doubleValue)
                    marker.title = club + "-" + passes
                    marker.snippet = name
                    marker.map = self.mapView
                    /*println(latitude)
                    println(longitude)
                    println(name)
                    println(club)
                    println(passes)*/
                }
            }
        }
        task.resume()
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
