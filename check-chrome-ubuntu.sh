#!/bin/bash

echo "======================================================"
echo "  VERIFICACIÓN DE CHROME EN UBUNTU 22.04"
echo "======================================================"
echo ""

echo "1. VERIFICAR SI CHROME ESTÁ INSTALADO"
echo "------------------------------------------------------"
which google-chrome
which google-chrome-stable
which chromium-browser
which chromium
echo ""

echo "2. VERIFICAR ARCHIVOS EN /usr/bin/"
echo "------------------------------------------------------"
ls -la /usr/bin/ | grep -E "(chrome|chromium)"
echo ""

echo "3. VERIFICAR PAQUETES INSTALADOS"
echo "------------------------------------------------------"
dpkg -l | grep -E "(chrome|chromium)"
echo ""

echo "4. VERIFICAR SI CHROME ESTÁ EN SNAP"
echo "------------------------------------------------------"
snap list | grep -E "(chrome|chromium)"
echo ""

echo "5. BUSCAR CHROME EN TODO EL SISTEMA"
echo "------------------------------------------------------"
find /usr /opt /snap -name "*chrome*" -type f 2>/dev/null | head -20
echo ""

echo "======================================================"
echo "  INSTRUCCIONES DE INSTALACIÓN"
echo "======================================================"
echo ""
echo "Si Chrome NO está instalado, ejecuta:"
echo ""
echo "# Opción 1: Google Chrome (RECOMENDADO)"
echo "wget https://dl.google.com/linux/direct/google-chrome-stable_current_amd64.deb"
echo "sudo dpkg -i google-chrome-stable_current_amd64.deb"
echo "sudo apt-get install -f -y"
echo ""
echo "# Opción 2: Chromium"
echo "sudo apt-get update"
echo "sudo apt-get install chromium-browser -y"
echo ""
echo "# Verificar instalación"
echo "google-chrome --version"
echo ""
