#!/bin/bash

# Configuration
LOG_DIR="./log/test"
mkdir -p "$LOG_DIR"
TIMESTAMP=$(date +"%Y%m%d_%H%M%S")
LOG_FILE="$LOG_DIR/test-$TIMESTAMP.log"
PHPUNIT="./vendor/bin/phpunit"

# Start Selenium
start_selenium() {
    if [ "$1" == "selenium" ]; then
        echo "[SELENIUM] Starting ChromeDriver..." | tee -a "$LOG_FILE"
        chromedriver --port=9515 >> "$LOG_DIR/chromedriver-$TIMESTAMP.log" 2>&1 &
        SELENIUM_PID=$!
        sleep 2
    fi
}

# Stop Selenium
stop_selenium() {
    if [ ! -z "$SELENIUM_PID" ]; then
        echo "[SELENIUM] Stopping ChromeDriver (PID: $SELENIUM_PID)" | tee -a "$LOG_FILE"
        kill $SELENIUM_PID
    fi
}

# Run tests
run_tests() {
    local type=$1
    local file=$2
    
    case "$type" in
        "unit")
            echo "[UNIT] Running all unit tests..." | tee -a "$LOG_FILE"
            $PHPUNIT --testsuite Unit | tee -a "$LOG_FILE"
            ;;
        "unit-single")
            echo "[UNIT] Running single test: $file" | tee -a "$LOG_FILE"
            $PHPUNIT "$file" | tee -a "$LOG_FILE"
            ;;
        "selenium")
            echo "[SELENIUM] Running all browser tests..." | tee -a "$LOG_FILE"
            $PHPUNIT --testsuite Browser | tee -a "$LOG_FILE"
            ;;
        "selenium-single")
            echo "[SELENIUM] Running single test: $file" | tee -a "$LOG_FILE"
            $PHPUNIT "$file" | tee -a "$LOG_FILE"
            ;;
        "all")
            echo "[ALL] Running all tests..." | tee -a "$LOG_FILE"
            $PHPUNIT | tee -a "$LOG_FILE"
            ;;
        *)
            echo "Usage:"
            echo "  $0 unit                     # Run all unit tests"
            echo "  $0 unit-single <file>       # Run single unit test"
            echo "  $0 selenium                 # Run all Selenium tests"
            echo "  $0 selenium-single <file>   # Run single Selenium test"
            echo "  $0 all                      # Run all tests"
            exit 1
            ;;
    esac
}

# Main execution
start_selenium "$1"
run_tests "$1" "$2"
stop_selenium

exit 0


