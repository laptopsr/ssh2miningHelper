# SSH2miningHelper

This project is created to monitor the status of mining builds for CPU.

## Install on OS Linux

Prerequisites
To run the program, you need to have the following packages installed: PHP, apache2, SSH2

`curl -sS https://getcomposer.org/installer | php`

Try: `composer update`

or

`php composer.phar update`

All builds should use Linux with the following packages installed:

`sudo apt install openssh-server lm-sensors`

Also, install the latest version of xmrig:
https://xmrig.com/download

And Cpuminer Rplant as well:
https://github.com/rplant8/cpuminer-opt-rplant/releases/

Demo: ![ScreenShot](/screenshot.png)

This README provides instructions for setting up and using the SSH2miningHelper project to monitor mining builds on CPU.

## Update SSH2miningHelper

The application is currently under development. To quickly update SSH2miningHelper you better use a special program Git (distributed version control).

Open the SSH2miningHelper directory in your terminal. Example:

`cd C:\Users\Home\Projects\ssh2miningHelper`

update using git: `git pull`

## Install on OS Windows

Install a web server and php. For example we will take [XAMPP](https://www.apachefriends.org/)

### Install XAMPP

Download XAMPP https://www.apachefriends.org/ru/index.html for Windows. When installing, select components:

- Apache
- PHP

After installation you need to launch the application *XAMPP Control Panel*. Then in the application panel you will find the Apache service and start it.

### Install GIT

To quickly install and update SSH2miningHelper you better use a special program Git (distributed version control).
You can download it from here https://git-scm.com/download/win version *"64-bit Git for Windows Setup"*;

During installation, you can leave all parameters unchanged. You can uncheck the *"Open Git Bash"* and *"Open Git GUI"* components  so that they are not integrated into Windows Explorer.

### Install SSH2miningHelper

The default directory for all web contents in XAMMP is `C:\xampp\htdocs`. 
In the XAMPP application panel find **Shell** and run it. It is special terminal with PHP support.

Open the web application directory in this terminal:

`cd C:\xampp\htdocs`

delete all files and folders using command:

`RMDIR /S /Q .`

then Git repository to clone into current directory:

`git clone https://github.com/laptopsr/ssh2miningHelper.git .`

The **ssh2miningHelper** project should appear in the *C:\xampp\htdocs* folder.

run the dependency installation command:

`composer update`

**copy the file "config.php.example" and rename it to "config.php". You will then have to customize the configuration for yourself**

You can check in browser (login "admin", password "admin"): http://localhost/

### Software installation on Windows for monitoring

**For now, data processing from the xmrig miner is supporte**

In the configuration file for the worker you need to add parameters:

`'os' => 'win', 'openhardware_port' => 8085,`

#### 1. Install [Open Hardware Monitor](https://openhardwaremonitor.org/) for monitoring temperature, then select:

- Options - Run On Windows Startup 
- Options - Remote Web Server - Run
- Options - Remote Web Server - Port = 8085
- File - Hardware - Only select CPU

To open port (on Windows):

    Navigate to Control Panel, System and Security and Windows Firewall.
    Select Advanced settings and highlight Inbound Rules in the left pane.
    Right click Inbound Rules and select New Rule.
    Add the port you need to open and click Next.
    Add the protocol (TCP) and the port number (8085) into the next window and click Next.
    Select Allow the connection in the next window and hit Next.
    Select the network type as you see fit and click Next.
    Name the rule and click Finish.

or run PowerShell as administrator and execute the command:

`New-NetFirewallRule -DisplayName "ALLOW TCP PORT 8085" -Direction inbound -Profile Any -Action Allow -LocalPort 8085 -Protocol TCP`

You can check it in your browser using your local IP:

http://192.168.100.103:8085/

#### 2. Install server OpenSSH (instraction in russian https://winitpro.ru/index.php/2019/10/17/windows-openssh-server/)

run PowerShell as administrator and execute the command:

`Get-WindowsCapability -Online | Where-Object Name -like 'OpenSSH.Server*' | Add-WindowsCapability –Online`

You can also install the OpenSSH server in Windows through the modern Settings panel (Settings -> Apps and features -> Optional features -> Add a feature, Applications -> Manage additional components -> Add a feature. Find OpenSSH Server in the list and click the Install button).

**To check that the OpenSSH server is installed, run:**

`Get-WindowsCapability -Online | ? Name -like 'OpenSSH.Ser*'`

result:

`Name  : OpenSSH.Server~~~~0.0.1.0
State : Installed`

**You need to change the sshd service startup type to automatic:**

`Set-Service -Name sshd -StartupType 'Automatic'`

`Start-Service sshd`

**Using nestat, make sure that the SSH server is now running on the system and is waiting for connections on port TCP:22:**

`netstat -na`

result:

`TCP    0.0.0.0:22             0.0.0.0:0              LISTENING`

**Verify that the Windows Defender Firewall rule is enabled to allow incoming connections to Windows on the TCP/22 port:**

`Get-NetFirewallRule -Name *OpenSSH-Server* |select Name, DisplayName, Description, Enabled`

result:

`OpenSSH-Server-In-TCP OpenSSH SSH Server (sshd) Inbound rule for OpenSSH SSH Server (sshd)    True`

If the rule is disabled (Enabled=False) or missing, you can create a new incoming rule with the New-NetFirewallRule command:

`New-NetFirewallRule -Name sshd -DisplayName 'OpenSSH Server (sshd)' -Enabled True -Direction Inbound -Protocol TCP -Action Allow -LocalPort 22`

**All that remains is to check the connection:**

`ssh home@192.168.100.103`
