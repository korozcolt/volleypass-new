#!/bin/bash

echo "🏐 VolleyPass Federation System - Test Runner"
echo "============================================="
echo ""

# Colores para output
RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
BLUE='\033[0;34m'
NC='\033[0m' # No Color

# Función para mostrar resultados
show_result() {
    if [ $1 -eq 0 ]; then
        echo -e "${GREEN}✅ $2 - PASSED${NC}"
    else
        echo -e "${RED}❌ $2 - FAILED${NC}"
        return 1
    fi
}

# Función para ejecutar test
run_test() {
    echo -e "${BLUE}🔍 Running: $1${NC}"
    eval $2
    show_result $? "$1"
    echo ""
}

# Limpiar y preparar base de datos
echo -e "${YELLOW}📋 Preparing test environment...${NC}"
php artisan migrate:fresh --force
echo ""

# Ejecutar tests individuales
echo -e "${YELLOW}🧪 Running Federation Tests...${NC}"
echo ""

# Test 1: Federation Service
run_test "Federation Service Tests" "php artisan test tests/Feature/Federation/FederationServiceTest.php --stop-on-failure"

# Test 2: Payment Validation Service
run_test "Payment Validation Service Tests" "php artisan test tests/Feature/Federation/PaymentValidationServiceTest.php --stop-on-failure"

# Test 3: Player Resource Tests
run_test "Player Resource Tests" "php artisan test tests/Feature/Federation/PlayerResourceTest.php --stop-on-failure"

# Test 4: Federation Test Suite
run_test "Federation Test Suite" "php artisan test tests/Feature/Federation/FederationTestSuite.php --stop-on-failure"

# Test 5: Seeder Test
run_test "Federation Test Seeder" "php artisan db:seed --class=FederationTestSeeder"

# Test 6: Command Test
run_test "Federation Command Test" "php artisan volleypass:test-federation"

echo ""
echo -e "${GREEN}🎉 All Federation Tests Completed!${NC}"
echo ""

# Mostrar estadísticas finales
echo -e "${BLUE}📊 Final Statistics:${NC}"
php artisan volleypass:test-federation

echo ""
echo -e "${YELLOW}💡 Next Steps:${NC}"
echo "1. Review any failed tests above"
echo "2. Check the admin panel at /admin"
echo "3. Continue with Day 2 development"
echo ""
