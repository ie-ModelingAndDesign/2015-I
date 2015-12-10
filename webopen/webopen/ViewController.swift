//
//  ViewController.swift
//  webopen
//
//  Created by shinya yuta on 12/1/15.
//  Copyright © 2015 shinya yuta. All rights reserved.
//

import UIKit

import SafariServices

class ViewController: UIViewController {

    @IBAction func tapBtn() {
        
        if let url = NSURL(string: "http://kyushu.seikyou.ne.jp/ryudai-coop/dinning/index.html") {
            
            let vc = SFSafariViewController(URL:url,entersReaderIfAvailable: true)
            presentViewController(vc, animated: true, completion: nil)
        }
    }
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view, typically from a nib.
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }


}