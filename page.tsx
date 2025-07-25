"use client"

import {
  Award,
  BarChart3,
  CheckCircle,
  Clock,
  Globe,
  Menu,
  Moon,
  Play,
  QrCode,
  Settings,
  Shield,
  Sun,
  TrendingUp,
  Trophy,
  Users,
  X,
} from "lucide-react"
import { Card, CardContent, CardDescription, CardHeader, CardTitle } from "@/components/ui/card"
import { Tabs, TabsContent, TabsList, TabsTrigger } from "@/components/ui/tabs"
import { useEffect, useState } from "react"

import { Badge } from "@/components/ui/badge"
import { Button } from "@/components/ui/button"
import { Progress } from "@/components/ui/progress"

export default function VolleyPassWelcome() {
  const [darkMode, setDarkMode] = useState(false)
  const [mobileMenuOpen, setMobileMenuOpen] = useState(false)
  const [activeTab, setActiveTab] = useState("federados")
  const [stats, setStats] = useState({
    jugadoras: 0,
    clubes: 0,
    torneos: 0,
    partidos: 0,
  })

  // Animated counters
  useEffect(() => {
    const targetStats = { jugadoras: 1247, clubes: 89, torneos: 23, partidos: 456 }
    const duration = 2000
    const steps = 60
    const stepDuration = duration / steps

    let currentStep = 0
    const timer = setInterval(() => {
      currentStep++
      const progress = currentStep / steps
      const easeOutQuart = 1 - Math.pow(1 - progress, 4)

      setStats({
        jugadoras: Math.floor(targetStats.jugadoras * easeOutQuart),
        clubes: Math.floor(targetStats.clubes * easeOutQuart),
        torneos: Math.floor(targetStats.torneos * easeOutQuart),
        partidos: Math.floor(targetStats.partidos * easeOutQuart),
      })

      if (currentStep >= steps) {
        clearInterval(timer)
        setStats(targetStats)
      }
    }, stepDuration)

    return () => clearInterval(timer)
  }, [])

  const toggleDarkMode = () => {
    setDarkMode(!darkMode)
    document.documentElement.classList.toggle("dark")
  }

  const scrollToSection = (sectionId: string) => {
    document.getElementById(sectionId)?.scrollIntoView({ behavior: "smooth" })
    setMobileMenuOpen(false)
  }

  return (
    <div className={`min-h-screen transition-colors duration-300 ${darkMode ? "dark" : ""}`}>
      {/* Header */}
      <header className="fixed top-0 w-full bg-white/90 dark:bg-gray-900/90 backdrop-blur-md z-50 border-b border-gray-200 dark:border-gray-700">
        <div className="container mx-auto px-4 lg:px-6 h-16 flex items-center justify-between">
          <div className="flex items-center space-x-2">
            <div className="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
              <Trophy className="w-5 h-5 text-white" />
            </div>
            <span className="text-xl font-bold bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
              VolleyPass Sucre
            </span>
          </div>

          {/* Desktop Navigation */}
          <nav className="hidden md:flex items-center space-x-6">
            <button
              onClick={() => scrollToSection("inicio")}
              className="text-sm font-medium hover:text-blue-600 transition-colors"
            >
              Inicio
            </button>
            <button
              onClick={() => scrollToSection("caracteristicas")}
              className="text-sm font-medium hover:text-blue-600 transition-colors"
            >
              Características
            </button>
            <button
              onClick={() => scrollToSection("demo")}
              className="text-sm font-medium hover:text-blue-600 transition-colors"
            >
              Demo
            </button>
            <button
              onClick={() => scrollToSection("progreso")}
              className="text-sm font-medium hover:text-blue-600 transition-colors"
            >
              Progreso
            </button>
            <button
              onClick={() => scrollToSection("contacto")}
              className="text-sm font-medium hover:text-blue-600 transition-colors"
            >
              Contacto
            </button>
          </nav>

          <div className="flex items-center space-x-4">
            <Button variant="ghost" size="sm" onClick={toggleDarkMode} className="w-9 h-9 p-0">
              {darkMode ? <Sun className="w-4 h-4" /> : <Moon className="w-4 h-4" />}
            </Button>

            {/* Mobile Menu Button */}
            <Button
              variant="ghost"
              size="sm"
              className="md:hidden w-9 h-9 p-0"
              onClick={() => setMobileMenuOpen(!mobileMenuOpen)}
            >
              {mobileMenuOpen ? <X className="w-4 h-4" /> : <Menu className="w-4 h-4" />}
            </Button>
          </div>
        </div>

        {/* Mobile Menu */}
        {mobileMenuOpen && (
          <div className="md:hidden bg-white dark:bg-gray-900 border-t border-gray-200 dark:border-gray-700">
            <nav className="container mx-auto px-4 py-4 space-y-2">
              <button
                onClick={() => scrollToSection("inicio")}
                className="block w-full text-left py-2 text-sm font-medium hover:text-blue-600 transition-colors"
              >
                Inicio
              </button>
              <button
                onClick={() => scrollToSection("caracteristicas")}
                className="block w-full text-left py-2 text-sm font-medium hover:text-blue-600 transition-colors"
              >
                Características
              </button>
              <button
                onClick={() => scrollToSection("demo")}
                className="block w-full text-left py-2 text-sm font-medium hover:text-blue-600 transition-colors"
              >
                Demo
              </button>
              <button
                onClick={() => scrollToSection("progreso")}
                className="block w-full text-left py-2 text-sm font-medium hover:text-blue-600 transition-colors"
              >
                Progreso
              </button>
              <button
                onClick={() => scrollToSection("contacto")}
                className="block w-full text-left py-2 text-sm font-medium hover:text-blue-600 transition-colors"
              >
                Contacto
              </button>
            </nav>
          </div>
        )}
      </header>

      {/* Hero Section */}
      <section
        id="inicio"
        className="pt-16 min-h-screen flex items-center bg-gradient-to-br from-blue-50 via-white to-purple-50 dark:from-gray-900 dark:via-gray-800 dark:to-gray-900"
      >
        <div className="container mx-auto px-4 lg:px-6">
          <div className="grid lg:grid-cols-2 gap-12 items-center">
            <div className="space-y-8">
              <div className="space-y-4">
                <Badge variant="secondary" className="bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200">
                  🏐 Plataforma Integral de Gestión
                </Badge>
                <h1 className="text-4xl md:text-6xl font-bold leading-tight">
                  <span className="bg-gradient-to-r from-blue-600 to-purple-600 bg-clip-text text-transparent">
                    VolleyPass
                  </span>
                  <br />
                  <span className="text-gray-900 dark:text-white">Sucre</span>
                </h1>
                <p className="text-xl text-gray-600 dark:text-gray-300 leading-relaxed">
                  Digitaliza y moderniza la gestión de la Liga de Voleibol de Sucre. Centraliza el registro,
                  verificación y gestión de jugadoras, entrenadores y clubes con transparencia y eficiencia total.
                </p>
              </div>

              <div className="flex flex-col sm:flex-row gap-4">
                <Button
                  size="lg"
                  className="bg-gradient-to-r from-blue-600 to-purple-600 hover:from-blue-700 hover:to-purple-700"
                >
                  <Play className="w-4 h-4 mr-2" />
                  Ver Demo
                </Button>
                <Button variant="outline" size="lg">
                  Conocer Más
                </Button>
              </div>

              {/* Stats */}
              <div className="grid grid-cols-2 md:grid-cols-4 gap-6 pt-8">
                <div className="text-center">
                  <div className="text-2xl md:text-3xl font-bold text-blue-600">{stats.jugadoras.toLocaleString()}</div>
                  <div className="text-sm text-gray-600 dark:text-gray-400">Jugadoras</div>
                </div>
                <div className="text-center">
                  <div className="text-2xl md:text-3xl font-bold text-purple-600">{stats.clubes}</div>
                  <div className="text-sm text-gray-600 dark:text-gray-400">Clubes</div>
                </div>
                <div className="text-center">
                  <div className="text-2xl md:text-3xl font-bold text-green-600">{stats.torneos}</div>
                  <div className="text-sm text-gray-600 dark:text-gray-400">Torneos</div>
                </div>
                <div className="text-center">
                  <div className="text-2xl md:text-3xl font-bold text-orange-600">{stats.partidos}</div>
                  <div className="text-sm text-gray-600 dark:text-gray-400">Partidos</div>
                </div>
              </div>
            </div>

            <div className="relative">
              <div className="relative z-10 bg-white dark:bg-gray-800 rounded-2xl shadow-2xl p-8 transform rotate-3 hover:rotate-0 transition-transform duration-300">
                <div className="space-y-4">
                  <div className="flex items-center space-x-3">
                    <div className="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-500 rounded-lg flex items-center justify-center">
                      <QrCode className="w-6 h-6 text-white" />
                    </div>
                    <div>
                      <h3 className="font-semibold">Verificación QR</h3>
                      <p className="text-sm text-gray-600 dark:text-gray-400">Instantánea en partidos</p>
                    </div>
                  </div>
                  <div className="bg-gray-100 dark:bg-gray-700 rounded-lg p-4">
                    <div className="grid grid-cols-8 gap-1">
                      {Array.from({ length: 64 }).map((_, i) => (
                        <div
                          key={i}
                          className={`aspect-square rounded-sm ${
                            Math.random() > 0.5 ? "bg-gray-800 dark:bg-white" : "bg-white dark:bg-gray-800"
                          }`}
                        />
                      ))}
                    </div>
                  </div>
                  <div className="text-center text-sm text-gray-600 dark:text-gray-400">
                    Escanea para verificar jugadora
                  </div>
                </div>
              </div>
              <div className="absolute inset-0 bg-gradient-to-br from-blue-400 to-purple-400 rounded-2xl transform rotate-6 opacity-20"></div>
            </div>
          </div>
        </div>
      </section>

      {/* Features Grid */}
      <section id="caracteristicas" className="py-20 bg-white dark:bg-gray-900">
        <div className="container mx-auto px-4 lg:px-6">
          <div className="text-center mb-16">
            <Badge variant="secondary" className="mb-4">
              Características Principales
            </Badge>
            <h2 className="text-3xl md:text-4xl font-bold mb-4">Ecosistema Digital Completo</h2>
            <p className="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
              Una plataforma integral que centraliza toda la gestión deportiva con tecnología moderna y procesos
              eficientes.
            </p>
          </div>

          <div className="grid md:grid-cols-2 lg:grid-cols-3 gap-8">
            <Card className="group hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
              <CardHeader>
                <div className="w-12 h-12 bg-blue-100 dark:bg-blue-900 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                  <Users className="w-6 h-6 text-blue-600 dark:text-blue-400" />
                </div>
                <CardTitle>Gestión Dual</CardTitle>
                <CardDescription>
                  Equipos federados (liga oficial) y descentralizados (ligas alternas) en una sola plataforma.
                </CardDescription>
              </CardHeader>
            </Card>

            <Card className="group hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
              <CardHeader>
                <div className="w-12 h-12 bg-purple-100 dark:bg-purple-900 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                  <Shield className="w-6 h-6 text-purple-600 dark:text-purple-400" />
                </div>
                <CardTitle>Sistema de Federación</CardTitle>
                <CardDescription>
                  Control completo de pagos, consignaciones y membresías con transparencia total.
                </CardDescription>
              </CardHeader>
            </Card>

            <Card className="group hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
              <CardHeader>
                <div className="w-12 h-12 bg-green-100 dark:bg-green-900 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                  <Settings className="w-6 h-6 text-green-600 dark:text-green-400" />
                </div>
                <CardTitle>Reglas Configurables</CardTitle>
                <CardDescription>
                  Cada liga define sus propias normativas y reglamentos de forma independiente.
                </CardDescription>
              </CardHeader>
            </Card>

            <Card className="group hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
              <CardHeader>
                <div className="w-12 h-12 bg-orange-100 dark:bg-orange-900 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                  <TrendingUp className="w-6 h-6 text-orange-600 dark:text-orange-400" />
                </div>
                <CardTitle>Control de Traspasos</CardTitle>
                <CardDescription>
                  Autorización obligatoria por parte de la liga para todos los movimientos de jugadoras.
                </CardDescription>
              </CardHeader>
            </Card>

            <Card className="group hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
              <CardHeader>
                <div className="w-12 h-12 bg-red-100 dark:bg-red-900 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                  <QrCode className="w-6 h-6 text-red-600 dark:text-red-400" />
                </div>
                <CardTitle>Verificación QR</CardTitle>
                <CardDescription>
                  Verificación instantánea de jugadoras en partidos mediante códigos QR únicos.
                </CardDescription>
              </CardHeader>
            </Card>

            <Card className="group hover:shadow-lg transition-all duration-300 hover:-translate-y-1">
              <CardHeader>
                <div className="w-12 h-12 bg-indigo-100 dark:bg-indigo-900 rounded-lg flex items-center justify-center mb-4 group-hover:scale-110 transition-transform">
                  <Trophy className="w-6 h-6 text-indigo-600 dark:text-indigo-400" />
                </div>
                <CardTitle>Gestión de Torneos</CardTitle>
                <CardDescription>
                  Organización completa de torneos oficiales y alternos con seguimiento en tiempo real.
                </CardDescription>
              </CardHeader>
            </Card>
          </div>
        </div>
      </section>

      {/* Interactive Demo */}
      <section id="demo" className="py-20 bg-gray-50 dark:bg-gray-800">
        <div className="container mx-auto px-4 lg:px-6">
          <div className="text-center mb-16">
            <Badge variant="secondary" className="mb-4">
              Demostración Interactiva
            </Badge>
            <h2 className="text-3xl md:text-4xl font-bold mb-4">Explora las Funcionalidades</h2>
            <p className="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
              Descubre cómo VolleyPass Sucre transforma la gestión deportiva con herramientas modernas e intuitivas.
            </p>
          </div>

          <div className="max-w-4xl mx-auto">
            <Tabs value={activeTab} onValueChange={setActiveTab} className="w-full">
              <TabsList className="grid w-full grid-cols-3">
                <TabsTrigger value="federados">Equipos Federados</TabsTrigger>
                <TabsTrigger value="descentralizados">Ligas Alternas</TabsTrigger>
                <TabsTrigger value="torneos">Gestión Torneos</TabsTrigger>
              </TabsList>

              <TabsContent value="federados" className="mt-8">
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center">
                      <Shield className="w-5 h-5 mr-2 text-blue-600" />
                      Gestión de Equipos Federados
                    </CardTitle>
                    <CardDescription>
                      Control oficial de clubes, jugadoras y entrenadores con certificación federativa.
                    </CardDescription>
                  </CardHeader>
                  <CardContent className="space-y-6">
                    <div className="grid md:grid-cols-2 gap-6">
                      <div className="space-y-4">
                        <h4 className="font-semibold flex items-center">
                          <CheckCircle className="w-4 h-4 mr-2 text-green-600" />
                          Registro Oficial
                        </h4>
                        <ul className="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                          <li>• Validación de documentos</li>
                          <li>• Certificación federativa</li>
                          <li>• Control de pagos y cuotas</li>
                          <li>• Historial deportivo completo</li>
                        </ul>
                      </div>
                      <div className="bg-gradient-to-br from-blue-50 to-purple-50 dark:from-blue-900/20 dark:to-purple-900/20 rounded-lg p-6">
                        <div className="text-center">
                          <div className="w-16 h-16 bg-blue-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Users className="w-8 h-8 text-white" />
                          </div>
                          <div className="text-2xl font-bold text-blue-600">847</div>
                          <div className="text-sm text-gray-600 dark:text-gray-400">Jugadoras Federadas</div>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </TabsContent>

              <TabsContent value="descentralizados" className="mt-8">
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center">
                      <Globe className="w-5 h-5 mr-2 text-purple-600" />
                      Ligas Descentralizadas
                    </CardTitle>
                    <CardDescription>
                      Gestión independiente de ligas alternas con reglas personalizables.
                    </CardDescription>
                  </CardHeader>
                  <CardContent className="space-y-6">
                    <div className="grid md:grid-cols-2 gap-6">
                      <div className="space-y-4">
                        <h4 className="font-semibold flex items-center">
                          <CheckCircle className="w-4 h-4 mr-2 text-green-600" />
                          Autonomía Total
                        </h4>
                        <ul className="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                          <li>• Reglas personalizadas</li>
                          <li>• Gestión independiente</li>
                          <li>• Torneos alternativos</li>
                          <li>• Flexibilidad organizativa</li>
                        </ul>
                      </div>
                      <div className="bg-gradient-to-br from-purple-50 to-pink-50 dark:from-purple-900/20 dark:to-pink-900/20 rounded-lg p-6">
                        <div className="text-center">
                          <div className="w-16 h-16 bg-purple-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Globe className="w-8 h-8 text-white" />
                          </div>
                          <div className="text-2xl font-bold text-purple-600">400</div>
                          <div className="text-sm text-gray-600 dark:text-gray-400">Jugadoras Alternas</div>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </TabsContent>

              <TabsContent value="torneos" className="mt-8">
                <Card>
                  <CardHeader>
                    <CardTitle className="flex items-center">
                      <Trophy className="w-5 h-5 mr-2 text-green-600" />
                      Gestión de Torneos
                    </CardTitle>
                    <CardDescription>Organización completa de competencias oficiales y alternativas.</CardDescription>
                  </CardHeader>
                  <CardContent className="space-y-6">
                    <div className="grid md:grid-cols-2 gap-6">
                      <div className="space-y-4">
                        <h4 className="font-semibold flex items-center">
                          <CheckCircle className="w-4 h-4 mr-2 text-green-600" />
                          Control Total
                        </h4>
                        <ul className="space-y-2 text-sm text-gray-600 dark:text-gray-400">
                          <li>• Programación automática</li>
                          <li>• Marcadores en tiempo real</li>
                          <li>• Estadísticas avanzadas</li>
                          <li>• Reportes automáticos</li>
                        </ul>
                      </div>
                      <div className="bg-gradient-to-br from-green-50 to-emerald-50 dark:from-green-900/20 dark:to-emerald-900/20 rounded-lg p-6">
                        <div className="text-center">
                          <div className="w-16 h-16 bg-green-600 rounded-full flex items-center justify-center mx-auto mb-4">
                            <Trophy className="w-8 h-8 text-white" />
                          </div>
                          <div className="text-2xl font-bold text-green-600">23</div>
                          <div className="text-sm text-gray-600 dark:text-gray-400">Torneos Activos</div>
                        </div>
                      </div>
                    </div>
                  </CardContent>
                </Card>
              </TabsContent>
            </Tabs>
          </div>
        </div>
      </section>

      {/* Project Progress */}
      <section id="progreso" className="py-20 bg-white dark:bg-gray-900">
        <div className="container mx-auto px-4 lg:px-6">
          <div className="text-center mb-16">
            <Badge variant="secondary" className="mb-4">
              Estado del Proyecto
            </Badge>
            <h2 className="text-3xl md:text-4xl font-bold mb-4">Progreso de Desarrollo</h2>
            <p className="text-xl text-gray-600 dark:text-gray-300 max-w-3xl mx-auto">
              Seguimiento transparente del avance en cada módulo del sistema VolleyPass Sucre.
            </p>
          </div>

          <div className="max-w-4xl mx-auto space-y-8">
            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span className="flex items-center">
                    <Users className="w-5 h-5 mr-2 text-blue-600" />
                    Gestión de Jugadoras
                  </span>
                  <Badge variant="secondary">95%</Badge>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <Progress value={95} className="mb-2" />
                <p className="text-sm text-gray-600 dark:text-gray-400">
                  Registro, verificación y gestión de perfiles completado. Implementando funciones avanzadas.
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span className="flex items-center">
                    <Shield className="w-5 h-5 mr-2 text-purple-600" />
                    Sistema de Federación
                  </span>
                  <Badge variant="secondary">87%</Badge>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <Progress value={87} className="mb-2" />
                <p className="text-sm text-gray-600 dark:text-gray-400">
                  Control de pagos y membresías funcional. Optimizando procesos de validación.
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span className="flex items-center">
                    <QrCode className="w-5 h-5 mr-2 text-green-600" />
                    Verificación QR
                  </span>
                  <Badge variant="secondary">92%</Badge>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <Progress value={92} className="mb-2" />
                <p className="text-sm text-gray-600 dark:text-gray-400">
                  Generación y lectura de códigos QR implementada. Realizando pruebas finales.
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span className="flex items-center">
                    <Trophy className="w-5 h-5 mr-2 text-orange-600" />
                    Gestión de Torneos
                  </span>
                  <Badge variant="secondary">78%</Badge>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <Progress value={78} className="mb-2" />
                <p className="text-sm text-gray-600 dark:text-gray-400">
                  Programación y seguimiento básico completado. Desarrollando estadísticas avanzadas.
                </p>
              </CardContent>
            </Card>

            <Card>
              <CardHeader>
                <CardTitle className="flex items-center justify-between">
                  <span className="flex items-center">
                    <BarChart3 className="w-5 h-5 mr-2 text-indigo-600" />
                    Reportes y Analytics
                  </span>
                  <Badge variant="secondary">65%</Badge>
                </CardTitle>
              </CardHeader>
              <CardContent>
                <Progress value={65} className="mb-2" />
                <p className="text-sm text-gray-600 dark:text-gray-400">
                  Dashboard básico implementado. Desarrollando reportes personalizados y métricas avanzadas.
                </p>
              </CardContent>
            </Card>
          </div>

          <div className="text-center mt-12">
            <div className="inline-flex items-center space-x-2 bg-green-100 dark:bg-green-900 text-green-800 dark:text-green-200 px-4 py-2 rounded-full">
              <Clock className="w-4 h-4" />
              <span className="text-sm font-medium">Lanzamiento estimado: Q2 2024</span>
            </div>
          </div>
        </div>
      </section>

      {/* CTA Section */}
      <section className="py-20 bg-gradient-to-br from-blue-600 to-purple-600 text-white">
        <div className="container mx-auto px-4 lg:px-6 text-center">
          <div className="max-w-3xl mx-auto space-y-8">
            <h2 className="text-3xl md:text-4xl font-bold">¿Listo para Modernizar tu Liga?</h2>
            <p className="text-xl opacity-90">
              Únete a la revolución digital del voleibol en Sucre. Transparencia, eficiencia y control total en una sola
              plataforma.
            </p>
            <div className="flex flex-col sm:flex-row gap-4 justify-center">
              <Button size="lg" variant="secondary" className="bg-white text-blue-600 hover:bg-gray-100">
                <Award className="w-4 h-4 mr-2" />
                Solicitar Demo
              </Button>
              <Button
                size="lg"
                variant="outline"
                className="border-white text-white hover:bg-white hover:text-blue-600 bg-transparent"
              >
                Contactar Equipo
              </Button>
            </div>
          </div>
        </div>
      </section>

      {/* Footer */}
      <footer id="contacto" className="bg-gray-900 text-white py-16">
        <div className="container mx-auto px-4 lg:px-6">
          <div className="grid md:grid-cols-4 gap-8">
            <div className="space-y-4">
              <div className="flex items-center space-x-2">
                <div className="w-8 h-8 bg-gradient-to-br from-blue-600 to-purple-600 rounded-lg flex items-center justify-center">
                  <Trophy className="w-5 h-5 text-white" />
                </div>
                <span className="text-xl font-bold">VolleyPass Sucre</span>
              </div>
              <p className="text-gray-400">Modernizando la gestión deportiva del voleibol en Sucre, Colombia.</p>
            </div>

            <div>
              <h3 className="font-semibold mb-4">Producto</h3>
              <ul className="space-y-2 text-gray-400">
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Características
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Precios
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Demo
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Documentación
                  </a>
                </li>
              </ul>
            </div>

            <div>
              <h3 className="font-semibold mb-4">Soporte</h3>
              <ul className="space-y-2 text-gray-400">
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Centro de Ayuda
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Contacto
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Capacitación
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Estado del Sistema
                  </a>
                </li>
              </ul>
            </div>

            <div>
              <h3 className="font-semibold mb-4">Legal</h3>
              <ul className="space-y-2 text-gray-400">
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Privacidad
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Términos
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Cookies
                  </a>
                </li>
                <li>
                  <a href="#" className="hover:text-white transition-colors">
                    Licencias
                  </a>
                </li>
              </ul>
            </div>
          </div>

          <div className="border-t border-gray-800 mt-12 pt-8 text-center text-gray-400">
            <p>&copy; 2024 VolleyPass Sucre. Todos los derechos reservados.</p>
          </div>
        </div>
      </footer>
    </div>
  )
}
