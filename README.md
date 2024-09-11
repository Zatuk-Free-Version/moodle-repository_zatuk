# Zatuk [Version 1]

    Maintained by: Naveen, Ranga Reddy
    Copyright: Moodle India
    License: http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later

    Welcome to the README for the zatuk plugin in Moodle! This document provides information about the zatuk plugin, how to install and use it, and customization options.

  # Description:

    This plugin revolutionizes the integration between the Zatuk application and Learning Management Systems (LMS), offering seamless connectivity and enhanced functionality for educational institutions and organizations. By installing this plugin and configuring the necessary details, users can effortlessly establish a robust connection between Zatuk and their preferred LMS platform.

    Upon installation, the plugin initiates a streamlined process: it automatically generates a dedicated organization within the Zatuk application tailored to the specific needs of the user. This organizational setup is pivotal, as it serves as the foundational structure through which Zatuk interacts with the LMS, ensuring clarity and efficiency in data exchange and operational workflows.

    Key to the functionality of the plugin is its provision of secure access credentials. Through a secure and automated mechanism, the plugin generates unique secret keys and authentication tokens. These credentials not only authenticate the connection between Zatuk and the LMS but also safeguard the integrity of data transmission.

# Short Description:

   This plugin transforms the integration between the Zatuk application and LMS platforms, enabling seamless connectivity and enhanced functionality. Upon installation, it automatically creates a dedicated organization in Zatuk tailored to the user’s needs and provides secure, automated access credentials to ensure a smooth and secure connection.

# Installation:

    1. Click on Site Administration from the navigation block.
    2. Select the Plugins tab from the list of tabs.
    3. Click on Install Plugin from the options. The page is redirected to the plugin installer.
    4. The user can install the plugin using the Choose File option, or he or she can drag and drop the downloaded zip file into the drag and drop box.
    5. After choosing the file, click on Continue until the upgrade to the new version is successful.
    6. On installation, Go to Manage Repositories in the site administration, enable the zatuk plugin, and click on save button, which will generate the token for zatuk webservices and secret, key in streaming server by creating the organization.

# Requirements:
    Based on moodle version user need to install the compatible zatuk plugin.

    To generate unique secret keys and authentication tokens, please follow these steps:

    1. Navigate to Site Administration > Plugins > Manage Repositories.
    2. Select "Enable and Visible" for the Zatuk Plugin.
    3. You will be redirected to a page with basic information for creating an organization in the Zatuk streaming 
       application. Click on "Next".
    4. To proceed, choose between two options: Free Subscription or Paid Subscription.
       Free Subscription: Includes basic setup and video streaming.
       Paid Subscription: Includes advanced features such as analytics, video ratings, likes, and dislikes.
    5. After selecting your subscription, click on "Get Started". The key and secret token will be generated.
    
    This process will set up the necessary credentials for secure integration between Zatuk and your LMS.

# How to install:

    1. Click on Site Administration from the navigation block.
    2. Select the Plugins tab from the list of tabs.
    3. Click on Install Plugin from the options. The page is redirected plugin installer.
    4. User can install the Plugin by Choose File option or he/she can drag and drop the downloaded zip file in the drag and drop box.
    5. After choosing the file click on continue till the Upgrade of the new version is successful.

# Supported Moodle versions:
    Moodle 4.0

# Code repository name:
    Moodle-repository_zatuk

# Dependencies:
    Moodle-mod_zatuk

# Cross-DB compatibility:
    Compatible with PGSQL, MSSQL, MYSQL and MariaDB


# Documentation URL:
    https://zatuk.com/knowledge-base/
