#!/bin/bash

# Colors
GREEN='\033[0;32m'
BLUE='\033[0;34m'
YELLOW='\033[1;33m'
RED='\033[0;31m'
NC='\033[0m' # No Color

# Function to display usage
show_usage() {
  echo -e "${BLUE}Fundoo Development Environment Manager${NC}"
  echo -e "Usage: ./dev.sh [command]"
  echo -e ""
  echo -e "Commands:"
  echo -e "  ${GREEN}start${NC}       Start the development environment"
  echo -e "  ${GREEN}stop${NC}        Stop the development environment"
  echo -e "  ${GREEN}restart${NC}     Restart the development environment"
  echo -e "  ${GREEN}build${NC}       Rebuild the development environment"
  echo -e "  ${GREEN}logs${NC}        View logs from all containers or a specific service"
  echo -e "  ${GREEN}shell${NC}       Open a shell in the app container"
  echo -e "  ${GREEN}artisan${NC}     Run an Artisan command"
  echo -e "  ${GREEN}composer${NC}    Run a Composer command"
  echo -e "  ${GREEN}npm${NC}         Run an NPM command"
  echo -e "  ${GREEN}db${NC}          Open PostgreSQL CLI"
  echo -e "  ${GREEN}redis${NC}       Open Redis CLI"
  echo -e "  ${GREEN}status${NC}      Show status of all containers"
  echo -e "  ${GREEN}help${NC}        Show this help message"
  echo -e ""
  echo -e "Examples:"
  echo -e "  ./dev.sh start"
  echo -e "  ./dev.sh artisan migrate"
  echo -e "  ./dev.sh logs app"
}

# Check if Docker is running
check_docker() {
  if ! docker info > /dev/null 2>&1; then
    echo -e "${RED}Error: Docker is not running.${NC}"
    exit 1
  fi
}

# Check if .env.local exists, if not copy from .env.example
check_env() {
  if [ ! -f .env.local ]; then
    if [ -f .env.example ]; then
      cp .env.example .env.local
      echo -e "${YELLOW}Created .env.local from .env.example${NC}"
    else
      echo -e "${YELLOW}Warning: No .env.local or .env.example found. Using default values.${NC}"
    fi
  fi
}

# Main function
main() {
  check_docker
  check_env

  case "$1" in
    start)
      echo -e "${GREEN}Starting development environment...${NC}"
      docker-compose -f docker-compose.dev.yml up -d
      echo -e "${GREEN}Development environment started!${NC}"
      echo -e "${BLUE}Access your application at:${NC} http://localhost"
      echo -e "${BLUE}MailHog interface at:${NC} http://localhost:8025"
      ;;
    stop)
      echo -e "${YELLOW}Stopping development environment...${NC}"
      docker-compose -f docker-compose.dev.yml down
      echo -e "${YELLOW}Development environment stopped.${NC}"
      ;;
    restart)
      echo -e "${YELLOW}Restarting development environment...${NC}"
      docker-compose -f docker-compose.dev.yml down
      docker-compose -f docker-compose.dev.yml up -d
      echo -e "${GREEN}Development environment restarted!${NC}"
      ;;
    build)
      echo -e "${BLUE}Rebuilding development environment...${NC}"
      docker-compose -f docker-compose.dev.yml down
      docker-compose -f docker-compose.dev.yml build --no-cache
      docker-compose -f docker-compose.dev.yml up -d
      echo -e "${GREEN}Development environment rebuilt and started!${NC}"
      ;;
    logs)
      if [ -z "$2" ]; then
        docker-compose -f docker-compose.dev.yml logs -f
      else
        docker-compose -f docker-compose.dev.yml logs -f "$2"
      fi
      ;;
    shell)
      echo -e "${BLUE}Opening shell in app container...${NC}"
      docker-compose -f docker-compose.dev.yml exec app bash
      ;;
    artisan)
      shift
      echo -e "${BLUE}Running Artisan command: ${YELLOW}$*${NC}"
      docker-compose -f docker-compose.dev.yml exec app php artisan "$@"
      ;;
    composer)
      shift
      echo -e "${BLUE}Running Composer command: ${YELLOW}$*${NC}"
      docker-compose -f docker-compose.dev.yml exec app composer "$@"
      ;;
    npm)
      shift
      echo -e "${BLUE}Running NPM command: ${YELLOW}$*${NC}"
      docker-compose -f docker-compose.dev.yml exec node npm "$@"
      ;;
    db)
      echo -e "${BLUE}Opening PostgreSQL CLI...${NC}"
      docker-compose -f docker-compose.dev.yml exec db psql -U postgres -d fundoo
      ;;
    redis)
      echo -e "${BLUE}Opening Redis CLI...${NC}"
      docker-compose -f docker-compose.dev.yml exec redis redis-cli
      ;;
    status)
      echo -e "${BLUE}Container status:${NC}"
      docker-compose -f docker-compose.dev.yml ps
      ;;
    help|*)
      show_usage
      ;;
  esac
}

# Execute main function with all arguments
main "$@"
