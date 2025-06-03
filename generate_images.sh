#!/bin/bash

# Create the images directory if it doesn't exist
mkdir -p public/images
mkdir -p public/images/projects
mkdir -p public/images/partners

# Generate hero background with modern gradient for Sponsoring24
convert -size 1920x1080 gradient:'#4F46E5'-'#7C3AED'-'#2563EB' -blur 0x30 \
    -fill white -gravity southeast -pointsize 30 -annotate +50+50 "Sponsoring24" \
    public/images/hero-bg.jpg

# Generate partner logos with professional look
for i in {1..6}; do
    # Create a white background with subtle gradient
    convert -size 200x100 gradient:'#FFFFFF'-'#F9FAFB' \
    -fill '#6366F1' -pointsize 20 -gravity center -font Ubuntu -annotate 0 "Partner $i" \
    -bordercolor '#E5E7EB' -border 1 \
    -alpha set -channel A -evaluate multiply 0.9 +channel \
    public/images/partners/$i.jpg
done

# Generate project images with modern design for Sponsoring24
for i in {1..9}; do
    # Create a base with a gradient using brand colors
    if [ $((i % 3)) -eq 0 ]; then
        # Purple to blue gradient
        convert -size 800x600 gradient:'#8B5CF6'-'#3B82F6' \
        -fill white -pointsize 40 -gravity center -font Ubuntu -annotate 0 "Campaign $i" \
        public/images/projects/$i.jpg
    elif [ $((i % 3)) -eq 1 ]; then
        # Green to teal gradient
        convert -size 800x600 gradient:'#10B981'-'#0EA5E9' \
        -fill white -pointsize 40 -gravity center -font Ubuntu -annotate 0 "Campaign $i" \
        public/images/projects/$i.jpg
    else
        # Purple to pink gradient
        convert -size 800x600 gradient:'#8B5CF6'-'#EC4899' \
        -fill white -pointsize 40 -gravity center -font Ubuntu -annotate 0 "Campaign $i" \
        public/images/projects/$i.jpg
    fi
    
    # Add a subtle pattern overlay
    convert public/images/projects/$i.jpg \
        -fill white -draw "rectangle 20,20 780,60" \
        -fill '#6366F1' -pointsize 24 -gravity northwest -annotate +30+30 "Sponsoring24" \
        public/images/projects/$i.jpg
done

# Generate illustrations with Sponsoring24 branding
convert -size 600x400 radial-gradient:'#FFFFFF'-'#C7D2FE' \
    -fill '#4F46E5' -pointsize 30 -gravity center -font Ubuntu -annotate 0 "Sponsoring24" \
    -fill '#6366F1' -pointsize 20 -gravity south -font Ubuntu -annotate +0+50 "Digital Sponsoring Platform" \
    public/images/illustration-1.jpg

convert -size 600x400 radial-gradient:'#FFFFFF'-'#A7F3D0' \
    -fill '#059669' -pointsize 30 -gravity center -font Ubuntu -annotate 0 "Sponsoring24" \
    -fill '#10B981' -pointsize 20 -gravity south -font Ubuntu -annotate +0+50 "Empowering Campaigns" \
    public/images/illustration-2.jpg

# Create illustrations - more professional
convert -size 400x400 xc:white -fill "#6d4aff" -gravity center -font Ubuntu -pointsize 40 -annotate 0 "Piggy Bank" \
-draw "circle 200,200 200,100" public/images/piggy-illustration.png

convert -size 400x400 xc:white -fill "#6d4aff" -gravity center -font Ubuntu -pointsize 40 -annotate 0 "Tools" \
-draw "polygon 100,100 300,100 300,300 100,300" public/images/tools-illustration.png

convert -size 400x400 xc:white -fill "#6d4aff" -gravity center -font Ubuntu -pointsize 40 -annotate 0 "Consultation" \
-draw "roundrectangle 100,100 300,300 20,20" public/images/consultation-illustration.png

# Create partner avatar - more professional
convert -size 200x200 xc:white -fill "#6d4aff" -gravity center -font Ubuntu -pointsize 40 -annotate 0 "Avatar" \
-draw "circle 100,100 100,50" public/images/partner-avatar.jpg

# Create collection donations image - more professional
convert -size 800x600 gradient:'#6d4aff-#2a1a87' -fill white -gravity center -font Ubuntu -pointsize 40 -annotate 0 "Collect Donations" public/images/collect-donations.jpg