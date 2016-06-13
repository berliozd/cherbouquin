# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure("2") do |config|

    # Base box
    config.vm.box = "ubuntu/trusty64"

    # Fine tune VBox
    config.vm.provider "virtualbox" do |vb|
        vb.customize ["modifyvm", :id, "--memory", "1024"]
        vb.customize ["modifyvm", :id, "--cpus", "2"]
        vb.customize ["modifyvm", :id, "--cpuexecutioncap", "90"]
    end

    # Sync folders in NFS
    config.vm.synced_folder ".", "/vagrant", id: "v-root", mount_options: ["rw", "tcp", "nolock", "noacl", "async"], type: "nfs", nfs_udp: false

    # Use password for SSH, needed for tools like Sequel Pro
    config.ssh.password = "vagrant"

    # Set host-only access IP
    config.vm.network "private_network", ip: "192.168.76.68"

    # Host post 7667 is for "soon" on a phone keyboard...
    config.vm.network :forwarded_port, host: 7668, guest: 80

    # Provisionning
    config.vm.provision :shell, path: "bootstrap-ubuntu-trusty64.sh"

end
