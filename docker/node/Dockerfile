FROM node:20

WORKDIR /var/www/html

# Install global dependencies
RUN npm install -g vite

# Copy package files
COPY package.json package-lock.json* ./

# Install project dependencies
RUN npm install

# Create ziggy directory for Vite
RUN mkdir -p /var/www/html/node_modules/ziggy && \
    touch /var/www/html/node_modules/ziggy/vue.m.js

# Set environment variables
ENV NODE_ENV=production
ENV HOST=0.0.0.0

# Command to run when container starts
CMD ["sh", "-c", "npm run build"]
