# -*- mode: ruby -*-
# vi: set ft=ruby :

hostname        = "phptest.dev"
server_ip       = "192.168.33.17"
server_timezone = "EST"

Vagrant.configure(2) do |config|
    config.vm.box = "bento/centos-6.7"
    config.vm.box_check_update = true

    config.vm.hostname = hostname
    config.vm.network "private_network", ip: server_ip
    config.vm.network "forwarded_port", guest: 8000, host: 8000

    config.vm.provision "ansible" do |ansible|
        ansible.playbook = "util/provision/setup.yml"
        ansible.verbose = "vvv"
        ansible.sudo = true
    end
end
