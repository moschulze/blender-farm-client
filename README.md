#BlenderFarm

##Please note
**This software is not finished yet!**
Don't use it in a production environment.

##Introduction
The BlenderFarm is a tool to easily split the rendering in blender to multiple machines. This way it is possible to render multiple frames at once on different machines to speed up the rendering process of your project.

You can easily upload your project via browser and configure the frames to render, the output format and the rendering engine. When you're happy with your configuration you can start the rendering process with a single click and check the progress in your browser.

The rending is done on clients. They request rendering tasks from the manage server and use a locally installed version of [Blender](http://www.blender.org) to complete them. After completion they upload the result to the server so it can be viewed and downloaded from you.

This repository contains the client software that does the rendering. The software for managing the projects could be found [here](https://github.com/moschulze/blender-farm).

##Requirements
You need the PHP-CLI to be installed in order top run this software. Also you have to download a copy of Blender from their [website](http://www.blender.org) to a directory of your choice (Take a note of the directory path. You will need it later).

##Installation
###File setup
Download or clone this repository onto the machine(s) you want to use as render clients. After that make sure that the file permissions are correct:

```sh
chmod a+rw files/
chmod +x bin/client
```

###Configuration
Copy the file config/parameters.yml.dist to config/parameters.yml and open it. Now edit the parameters to match your setup.

You definitely need to change the _api_url_ to your manage server and the _path_to_blender_ to the path to your blender executable.

```yml
parameters:
  api_url: http://manage-server-host/api/
  path_to_blender: /path/blender
```

###Done
Congratulation, you successfully have installed the BlenderFarm client!

##Run
To run the client simply execute the following command:

```sh
bin/client
```

It will request rendering tasks from the server. When the server does't have mor work top do, the client will end itself.

##ToDo
- Run forever
- Test of the setup