#!/bin/bash

# Create hero background
convert -size 1920x1080 gradient:purple-blue -blur 0x5 public/images/hero-bg.jpg

# Create partner logos (10 PNG files)
for i in {1..10}; do
    convert -size 200x100 xc:white -fill "#6d4aff" -gravity center -pointsize 40 -annotate 0 "Partner $i" public/images/partner$i.png
done

# Create project images
for i in {1..3}; do
    convert -size 800x600 gradient:purple-blue -fill white -gravity center -pointsize 40 -annotate 0 "Project $i" public/images/project$i.jpg
done

# Create illustrations
convert -size 400x400 xc:white -fill "#6d4aff" -gravity center -pointsize 40 -annotate 0 "Piggy Bank" public/images/piggy-illustration.png
convert -size 400x400 xc:white -fill "#6d4aff" -gravity center -pointsize 40 -annotate 0 "Tools" public/images/tools-illustration.png
convert -size 400x400 xc:white -fill "#6d4aff" -gravity center -pointsize 40 -annotate 0 "Consultation" public/images/consultation-illustration.png

# Create partner avatar
convert -size 200x200 xc:white -fill "#6d4aff" -gravity center -pointsize 40 -annotate 0 "Avatar" public/images/partner-avatar.jpg

# Create collection donations image
convert -size 800x600 gradient:purple-blue -fill white -gravity center -pointsize 40 -annotate 0 "Collect Donations" public/images/collect-donations.jpg 