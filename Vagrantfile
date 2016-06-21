# -*- mode: ruby -*-
# vi: set ft=ruby :

Vagrant.configure(2) do |config|
    config.vm.box = "bento/centos-6.7"
    config.vm.box_check_update = true
    config.vm.network "private_network", ip: "192.168.33.17"
    config.vm.network "forwarded_port", guest: 9000, host: 9000

    config.vm.provision "ansible" do |ansible|
        ansible.playbook = "util/provision/setup.yml"
        ansible.verbose = "vvv"
        ansible.sudo = true
    end
end
