//
//  KeychainWrapper.h
//  Apple's Keychain Services Programming Guide
//
//  Created by Tim Mitra on 11/17/14.
//  Copyright (c) 2014 Apple. All rights reserved.
//
// found from: 
// http://www.raywenderlich.com/92667/securing-ios-data-keychain-touch-id-1password
// used to securely store user password and username locally

#import <Foundation/Foundation.h>
#import <Security/Security.h>

@interface KeychainWrapper : NSObject

- (void)mySetObject:(id)inObject forKey:(id)key;
- (id)myObjectForKey:(id)key;
- (void)writeToKeychain;

@end
