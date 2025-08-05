import React, { useState, useEffect } from 'react';
import { ChevronLeft, ChevronRight, Check, Users, MapPin, Settings, Trophy, Calendar, FileText } from 'lucide-react';

interface ClubWizardStep {
  id: number;
  title: string;
  description: string;
  icon: React.ComponentType<any>;
  component: React.ComponentType<any>;
}

interface ClubSetupWizardProps {
  clubId?: string;
  onComplete?: () => void;
  initialStep?: number;
}

const ClubSetupWizard: React.FC<ClubSetupWizardProps> = ({ clubId, onComplete, initialStep = 1 }) => {
  const [currentStep, setCurrentStep] = useState(initialStep);
  const [completedSteps, setCompletedSteps] = useState<number[]>([]);
  const [formData, setFormData] = useState<Record<string, any>>({});
  const [loading, setLoading] = useState(false);

  const steps: ClubWizardStep[] = [
    {
      id: 1,
      title: 'Información Básica',
      description: 'Datos generales del club',
      icon: Settings,
      component: Step1BasicInfo
    },
    {
      id: 2,
      title: 'Ubicación',
      description: 'Dirección y contacto',
      icon: MapPin,
      component: Step2Location
    },
    {
      id: 3,
      title: 'Categorías',
      description: 'Categorías que maneja el club',
      icon: Trophy,
      component: Step3Categories
    },
    {
      id: 4,
      title: 'Equipos Iniciales',
      description: 'Crear equipos base',
      icon: Users,
      component: Step4Teams
    },
    {
      id: 5,
      title: 'Temporadas',
      description: 'Configurar temporadas',
      icon: Calendar,
      component: Step5Seasons
    },
    {
      id: 6,
      title: 'Documentación',
      description: 'Documentos y políticas',
      icon: FileText,
      component: Step6Documentation
    }
  ];

  const progress = (currentStep / steps.length) * 100;

  const handleNext = async () => {
    setLoading(true);
    try {
      // Validar y guardar datos del paso actual
      const stepData = await validateCurrentStep();
      
      if (currentStep < steps.length) {
        setCompletedSteps(prev => [...prev, currentStep]);
        setCurrentStep(currentStep + 1);
      } else {
        // Completar configuración
        await completeClubSetup();
        onComplete?.();
      }
    } catch (error) {
      console.error('Error en paso:', error);
    } finally {
      setLoading(false);
    }
  };

  const handlePrevious = () => {
    if (currentStep > 1) {
      setCurrentStep(currentStep - 1);
    }
  };

  const handleStepClick = (stepId: number) => {
    if (stepId <= currentStep || completedSteps.includes(stepId)) {
      setCurrentStep(stepId);
    }
  };

  const updateFormData = (stepData: Record<string, any>) => {
    setFormData(prev => ({ ...prev, ...stepData }));
  };

  const validateCurrentStep = async (): Promise<any> => {
    // Implementar validación específica por paso
    return formData;
  };

  const completeClubSetup = async () => {
    // Enviar todos los datos al backend
    const response = await fetch('/api/club/setup/complete', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || ''
      },
      body: JSON.stringify({
        clubId,
        data: formData
      })
    });

    if (!response.ok) {
      throw new Error('Error completando configuración del club');
    }
  };

  const CurrentStepComponent = steps[currentStep - 1]?.component;

  return (
    <div className="min-h-screen bg-gradient-to-br from-green-50 to-blue-100">
      <div className="container mx-auto px-4 py-8">
        {/* Header */}
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">
            Configuración Inicial del Club
          </h1>
          <p className="text-gray-600">
            Configure su club paso a paso para comenzar a usar VolleyPass
          </p>
        </div>

        {/* Progress Bar */}
        <div className="mb-8">
          <div className="bg-white rounded-lg shadow-sm p-4">
            <div className="flex justify-between items-center mb-2">
              <span className="text-sm font-medium text-gray-700">
                Paso {currentStep} de {steps.length}
              </span>
              <span className="text-sm text-gray-500">
                {Math.round(progress)}% completado
              </span>
            </div>
            <div className="w-full bg-gray-200 rounded-full h-2">
              <div 
                className="bg-green-600 h-2 rounded-full transition-all duration-300"
                style={{ width: `${progress}%` }}
              ></div>
            </div>
          </div>
        </div>

        {/* Steps Navigation */}
        <div className="mb-8">
          <div className="flex flex-wrap justify-center gap-2">
            {steps.map((step) => {
              const isCompleted = completedSteps.includes(step.id);
              const isCurrent = step.id === currentStep;
              const isAccessible = step.id <= currentStep || isCompleted;
              
              return (
                <button
                  key={step.id}
                  onClick={() => handleStepClick(step.id)}
                  disabled={!isAccessible}
                  className={`
                    flex items-center space-x-2 px-3 py-2 rounded-lg text-sm font-medium transition-all
                    ${
                      isCompleted
                        ? 'bg-green-100 text-green-800 border border-green-200'
                        : isCurrent
                        ? 'bg-blue-100 text-blue-800 border border-blue-200'
                        : isAccessible
                        ? 'bg-gray-100 text-gray-600 border border-gray-200 hover:bg-gray-200'
                        : 'bg-gray-50 text-gray-400 border border-gray-100 cursor-not-allowed'
                    }
                  `}
                >
                  <step.icon className="w-4 h-4" />
                  <span className="hidden sm:inline">{step.title}</span>
                  {isCompleted && <Check className="w-4 h-4" />}
                </button>
              );
            })}
          </div>
        </div>

        {/* Main Content */}
        <div className="bg-white rounded-lg shadow-lg">
          <div className="p-6 border-b border-gray-200">
            <div className="flex items-center space-x-3">
              {React.createElement(steps[currentStep - 1]?.icon, { className: "w-6 h-6 text-green-600" })}
              <div>
                <h2 className="text-xl font-semibold text-gray-900">
                  {steps[currentStep - 1]?.title}
                </h2>
                <p className="text-gray-600">
                  {steps[currentStep - 1]?.description}
                </p>
              </div>
            </div>
          </div>

          <div className="p-6">
            {CurrentStepComponent && (
              <CurrentStepComponent 
                formData={formData}
                updateFormData={updateFormData}
                onNext={handleNext}
                onPrevious={handlePrevious}
                clubId={clubId}
              />
            )}
          </div>

          {/* Navigation Buttons */}
          <div className="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
            <button
              onClick={handlePrevious}
              disabled={currentStep === 1 || loading}
              className="flex items-center space-x-2 px-4 py-2 text-gray-600 hover:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronLeft className="w-4 h-4" />
              <span>Anterior</span>
            </button>

            <button
              onClick={handleNext}
              disabled={loading}
              className="flex items-center space-x-2 px-6 py-2 bg-green-600 hover:bg-green-700 text-white rounded-lg font-medium transition-colors disabled:opacity-50"
            >
              {loading ? (
                <div className="w-4 h-4 border-2 border-white border-t-transparent rounded-full animate-spin"></div>
              ) : (
                <>
                  <span>{currentStep === steps.length ? 'Finalizar' : 'Siguiente'}</span>
                  <ChevronRight className="w-4 h-4" />
                </>
              )}
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

// Componentes de cada paso
const Step1BasicInfo: React.FC<any> = ({ formData, updateFormData }) => {
  const [data, setData] = useState({
    name: formData.name || '',
    short_name: formData.short_name || '',
    founded_year: formData.founded_year || '',
    description: formData.description || '',
    colors: formData.colors || { primary: '#000000', secondary: '#ffffff' }
  });

  useEffect(() => {
    updateFormData(data);
  }, [data]);

  return (
    <div className="space-y-6">
      <h3 className="text-lg font-medium">Información Básica del Club</h3>
      
      <div className="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Nombre del Club *
          </label>
          <input
            type="text"
            value={data.name}
            onChange={(e) => setData(prev => ({ ...prev, name: e.target.value }))}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            placeholder="Ej: Club Deportivo Volcanes"
          />
        </div>
        
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Nombre Corto *
          </label>
          <input
            type="text"
            value={data.short_name}
            onChange={(e) => setData(prev => ({ ...prev, short_name: e.target.value }))}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            placeholder="Ej: Volcanes"
          />
        </div>
        
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Año de Fundación
          </label>
          <input
            type="number"
            value={data.founded_year}
            onChange={(e) => setData(prev => ({ ...prev, founded_year: e.target.value }))}
            className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
            placeholder="2020"
          />
        </div>
        
        <div>
          <label className="block text-sm font-medium text-gray-700 mb-2">
            Color Primario
          </label>
          <input
            type="color"
            value={data.colors.primary}
            onChange={(e) => setData(prev => ({ 
              ...prev, 
              colors: { ...prev.colors, primary: e.target.value }
            }))}
            className="w-full h-10 border border-gray-300 rounded-lg"
          />
        </div>
      </div>
      
      <div>
        <label className="block text-sm font-medium text-gray-700 mb-2">
          Descripción
        </label>
        <textarea
          value={data.description}
          onChange={(e) => setData(prev => ({ ...prev, description: e.target.value }))}
          rows={4}
          className="w-full px-3 py-2 border border-gray-300 rounded-lg focus:ring-2 focus:ring-green-500 focus:border-transparent"
          placeholder="Descripción del club, historia, objetivos..."
        />
      </div>
    </div>
  );
};

const Step2Location: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Ubicación y Contacto</h3>
    <p className="text-gray-600">Configure la información de contacto y ubicación del club.</p>
    {/* Implementar formulario de ubicación */}
  </div>
);

const Step3Categories: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Categorías del Club</h3>
    <p className="text-gray-600">Seleccione las categorías en las que participará el club.</p>
    {/* Implementar selección de categorías */}
  </div>
);

const Step4Teams: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Equipos Iniciales</h3>
    <p className="text-gray-600">Cree los equipos base del club.</p>
    {/* Implementar creación de equipos */}
  </div>
);

const Step5Seasons: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Configuración de Temporadas</h3>
    <p className="text-gray-600">Configure las temporadas de competencia.</p>
    {/* Implementar configuración de temporadas */}
  </div>
);

const Step6Documentation: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Documentación y Políticas</h3>
    <p className="text-gray-600">Configure documentos y políticas del club.</p>
    {/* Implementar carga de documentos */}
  </div>
);

export default ClubSetupWizard;