import React, { useState, useEffect } from 'react';
import { ChevronLeft, ChevronRight, Check, Settings, Users, Trophy, Shield, Database, Globe, Zap } from 'lucide-react';

interface WizardStep {
  id: number;
  title: string;
  description: string;
  icon: React.ComponentType<any>;
  component: React.ComponentType<any>;
}

interface SetupWizardProps {
  onComplete?: () => void;
  initialStep?: number;
}

const SetupWizard: React.FC<SetupWizardProps> = ({ onComplete, initialStep = 1 }) => {
  const [currentStep, setCurrentStep] = useState(initialStep);
  const [completedSteps, setCompletedSteps] = useState<number[]>([]);
  const [formData, setFormData] = useState<Record<string, any>>({});

  const steps: WizardStep[] = [
    {
      id: 1,
      title: 'Configuración General',
      description: 'Configuración básica del sistema',
      icon: Settings,
      component: Step1General
    },
    {
      id: 2,
      title: 'Configuración de Liga',
      description: 'Datos de la liga principal',
      icon: Trophy,
      component: Step2League
    },
    {
      id: 3,
      title: 'Departamentos y Ciudades',
      description: 'Configuración geográfica',
      icon: Globe,
      component: Step3Geography
    },
    {
      id: 4,
      title: 'Categorías',
      description: 'Categorías de competencia',
      icon: Users,
      component: Step4Categories
    },
    {
      id: 5,
      title: 'Configuración de Seguridad',
      description: 'Permisos y roles',
      icon: Shield,
      component: Step5Security
    },
    {
      id: 6,
      title: 'Base de Datos',
      description: 'Configuración de datos iniciales',
      icon: Database,
      component: Step6Database
    },
    {
      id: 7,
      title: 'Finalización',
      description: 'Revisión y activación',
      icon: Zap,
      component: Step7Completion
    }
  ];

  const progress = (currentStep / steps.length) * 100;

  const handleNext = () => {
    if (currentStep < steps.length) {
      setCompletedSteps(prev => [...prev, currentStep]);
      setCurrentStep(currentStep + 1);
    } else {
      onComplete?.();
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

  const CurrentStepComponent = steps[currentStep - 1]?.component;

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
      <div className="container mx-auto px-4 py-8">
        {/* Header */}
        <div className="text-center mb-8">
          <h1 className="text-3xl font-bold text-gray-900 mb-2">
            Configuración Inicial de VolleyPass
          </h1>
          <p className="text-gray-600">
            Configura tu sistema paso a paso
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
                className="bg-blue-600 h-2 rounded-full transition-all duration-300"
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
              {React.createElement(steps[currentStep - 1]?.icon, { className: "w-6 h-6 text-blue-600" })}
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
              />
            )}
          </div>

          {/* Navigation Buttons */}
          <div className="px-6 py-4 bg-gray-50 border-t border-gray-200 flex justify-between">
            <button
              onClick={handlePrevious}
              disabled={currentStep === 1}
              className="flex items-center space-x-2 px-4 py-2 text-gray-600 hover:text-gray-800 disabled:opacity-50 disabled:cursor-not-allowed"
            >
              <ChevronLeft className="w-4 h-4" />
              <span>Anterior</span>
            </button>

            <button
              onClick={handleNext}
              className="flex items-center space-x-2 px-6 py-2 bg-blue-600 hover:bg-blue-700 text-white rounded-lg font-medium transition-colors"
            >
              <span>{currentStep === steps.length ? 'Finalizar' : 'Siguiente'}</span>
              <ChevronRight className="w-4 h-4" />
            </button>
          </div>
        </div>
      </div>
    </div>
  );
};

// Placeholder components for each step
const Step1General: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Configuración General del Sistema</h3>
    <p className="text-gray-600">Configure los parámetros básicos de su sistema VolleyPass.</p>
    {/* Implementar formulario específico */}
  </div>
);

const Step2League: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Configuración de Liga</h3>
    <p className="text-gray-600">Configure los datos de su liga principal.</p>
    {/* Implementar formulario específico */}
  </div>
);

const Step3Geography: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Departamentos y Ciudades</h3>
    <p className="text-gray-600">Configure la información geográfica.</p>
    {/* Implementar formulario específico */}
  </div>
);

const Step4Categories: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Categorías de Competencia</h3>
    <p className="text-gray-600">Configure las categorías disponibles.</p>
    {/* Implementar formulario específico */}
  </div>
);

const Step5Security: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Configuración de Seguridad</h3>
    <p className="text-gray-600">Configure permisos y roles del sistema.</p>
    {/* Implementar formulario específico */}
  </div>
);

const Step6Database: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Configuración de Base de Datos</h3>
    <p className="text-gray-600">Configure los datos iniciales del sistema.</p>
    {/* Implementar formulario específico */}
  </div>
);

const Step7Completion: React.FC<any> = ({ formData, updateFormData }) => (
  <div className="space-y-6">
    <h3 className="text-lg font-medium">Finalización</h3>
    <p className="text-gray-600">Revise la configuración y active el sistema.</p>
    {/* Implementar resumen y finalización */}
  </div>
);

export default SetupWizard;