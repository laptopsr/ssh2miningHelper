# SSH2miningHelper

This project is created to monitor the status of mining builds for CPU.

Prerequisites
To run the program, you need to have the following packages installed: PHP, apache2, SSH2

`curl -sS https://getcomposer.org/installer | php`

Try: `composer update`

or

`php composer.phar update`

All builds should use Linux with the following packages installed:

sudo apt install openssh-server lm-sensors

Also, install the latest version of xmrig:
https://xmrig.com/download

And Cpuminer Rplant as well:
https://github.com/rplant8/cpuminer-opt-rplant/releases/

Demo:  https://github.com/laptopsr/ssh2miningHelper/blob/main/screenshot.png

This README provides instructions for setting up and using the SSH2miningHelper project to monitor mining builds on CPU.


## Software installation on Windows for monitoring

**For now, data processing from the xmrig miner is supporte**

In the configuration file for the worker you need to add parameters:

`'os' => 'win', 'openhardware_port' => 8085,`

### 1. Install [Open Hardware Monitor](https://openhardwaremonitor.org/) for monitoring temperature, then select:

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

### 2. Install server OpenSSH (instraction in russian https://winitpro.ru/index.php/2019/10/17/windows-openssh-server/)

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
