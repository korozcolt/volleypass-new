import React from 'react';

interface Player {
  id: string;
  name: string;
  document_number: string;
  birth_date: string;
  birth_city?: string;
  blood_type?: {
    label: string;
  };
  blood_rh?: {
    symbol: string;
  };
  current_club?: {
    name: string;
    logo?: string;
  };
  current_card?: {
    card_number: string;
    issue_date: string;
    expiry_date: string;
    status: string;
  };
  photo_url?: string;
}

interface PlayerCardProps {
  player: Player;
}

const PlayerCard: React.FC<PlayerCardProps> = ({ player }) => {
  const bloodType = player.blood_type && player.blood_rh 
    ? `${player.blood_type.label}${player.blood_rh.symbol}`
    : 'AB+';

  const formatDate = (dateString: string) => {
    return new Date(dateString).toLocaleDateString('es-ES', {
      day: '2-digit',
      month: '2-digit',
      year: 'numeric'
    });
  };

  const handlePrint = () => {
    window.print();
  };

  const handleDownloadPDF = () => {
    window.location.href = `/player/${player.id}/card/download`;
  };

  return (
    <div className="min-h-screen bg-gradient-to-br from-blue-50 to-green-50 flex items-center justify-center p-4">
      {/* Control Buttons */}
      <div className="fixed top-4 right-4 z-50 flex space-x-2 no-print">
        <button
          onClick={handlePrint}
          className="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors duration-200 flex items-center space-x-2"
        >
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" />
          </svg>
          <span>Imprimir</span>
        </button>
        <button
          onClick={handleDownloadPDF}
          className="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg shadow-lg transition-colors duration-200 flex items-center space-x-2"
        >
          <svg className="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path strokeLinecap="round" strokeLinejoin="round" strokeWidth={2} d="M12 10v6m0 0l-3-3m3 3l3-3m2 8H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
          </svg>
          <span>PDF</span>
        </button>
      </div>

      {/* Player Card */}
      <div className="card-container relative w-[340px] h-[540px] bg-white rounded-2xl shadow-2xl overflow-hidden transform transition-all duration-300 hover:scale-105">
        {/* Geometric Background */}
        <div className="absolute inset-0 bg-gradient-to-br from-blue-50 to-green-50">
          <div className="absolute top-0 right-0 w-32 h-32 bg-green-100 rounded-full opacity-30 transform translate-x-16 -translate-y-16"></div>
          <div className="absolute bottom-0 left-0 w-24 h-24 bg-blue-100 rounded-full opacity-30 transform -translate-x-12 translate-y-12"></div>
          <div className="absolute top-1/2 left-1/2 w-40 h-40 bg-gradient-to-r from-green-50 to-blue-50 rounded-full opacity-20 transform -translate-x-1/2 -translate-y-1/2"></div>
        </div>

        {/* Card Number Badge */}
        <div className="absolute top-4 left-4 z-20">
          <div className="bg-green-600 text-white px-3 py-1 rounded-full text-xs font-bold shadow-lg">
            No. {player.current_card?.card_number || 'N/A'}
          </div>
        </div>

        {/* League Logo Area */}
        <div className="absolute top-4 right-4 z-20">
          <div className="w-16 h-16 bg-white rounded-full shadow-lg flex items-center justify-center border-2 border-gray-200">
            <div className="text-center">
              <div className="text-green-600 font-black text-xs leading-none">LVS</div>
              <div className="text-[8px] text-green-600 font-medium leading-none mt-0.5">LIGA DE VOLEIBOL</div>
              <div className="text-[8px] text-green-600 font-medium leading-none">DE SUCRE</div>
              <div className="text-[6px] text-green-600 font-bold mt-0.5">2024</div>
            </div>
          </div>
        </div>

        {/* Player Photo */}
        <div className="relative z-10 pt-20 pb-4 px-6">
          <div className="w-24 h-24 mx-auto mb-4 rounded-full overflow-hidden border-4 border-white shadow-lg bg-gray-100">
            {player.photo_url ? (
              <img 
                src={player.photo_url} 
                alt={player.name}
                className="w-full h-full object-cover"
              />
            ) : (
              <div className="w-full h-full flex items-center justify-center bg-gray-200">
                <svg className="w-12 h-12 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                  <path fillRule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clipRule="evenodd" />
                </svg>
              </div>
            )}
          </div>
        </div>

        {/* Player Information */}
        <div className="relative z-10 px-6 text-center">
          {/* Player Name */}
          <h2 className="text-xl font-bold text-gray-800 mb-2 leading-tight">
            {player.name.toUpperCase()}
          </h2>

          {/* Personal Data with Blood Type */}
          <div className="text-sm text-gray-600 mb-4 space-y-1">
            <div>ID: {player.document_number}</div>
            {player.birth_city && <div>{player.birth_city}</div>}
            <div>FECHA NAC: {formatDate(player.birth_date)}</div>
            {/* Blood Type integrated in personal data */}
            <div className="flex items-center justify-center gap-2 mt-2">
              <span>TIPO SANGRE:</span>
              <div className="bg-red-100 text-red-700 px-2 py-1 rounded-full text-xs font-bold border border-red-300">
                {bloodType}
              </div>
            </div>
          </div>

          {/* Club Information */}
          {player.current_club && (
            <div className="flex items-center justify-center mb-6 space-x-3">
              {/* Club Logo Placeholder */}
              <div className="w-10 h-10 bg-gray-200 rounded-full flex items-center justify-center border-2 border-green-600 flex-shrink-0">
                {player.current_club.logo ? (
                  <img 
                    src={player.current_club.logo} 
                    alt={player.current_club.name}
                    className="w-full h-full object-cover rounded-full"
                  />
                ) : (
                  <svg className="w-5 h-5 text-green-600" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                  </svg>
                )}
              </div>
              <div className="text-white bg-green-600 px-3 py-1 rounded-lg font-bold text-sm">
                {player.current_club.name.toUpperCase()}
              </div>
            </div>
          )}
        </div>

        {/* Bottom Section */}
        <div className="absolute bottom-0 left-0 right-0">
          {/* Volleyball Logo - Fixed positioning */}
          <div className="absolute bottom-16 right-6 z-20">
            <div className="w-14 h-14 bg-green-600 rounded-full shadow-lg flex items-center justify-center">
              <div className="w-10 h-10 bg-white rounded-full flex items-center justify-center">
                <svg className="w-6 h-6 text-green-600" viewBox="0 0 24 24" fill="currentColor">
                  <circle cx="12" cy="12" r="10" stroke="currentColor" strokeWidth="1" fill="none"/>
                  <path d="M2 12h20M12 2a15.3 15.3 0 0 1 4 10 15.3 15.3 0 0 1-4 10 15.3 15.3 0 0 1-4-10 15.3 15.3 0 0 1 4-10z" stroke="currentColor" strokeWidth="1" fill="none"/>
                </svg>
              </div>
            </div>
          </div>

          {/* Footer */}
          <div className="bg-green-600 text-white p-4 flex justify-between items-center">
            <div className="text-sm font-bold">LVS</div>
            <div className="text-xs">
              VENCE: {player.current_card?.expiry_date ? formatDate(player.current_card.expiry_date) : 'N/A'}
            </div>
          </div>
        </div>
      </div>

      <style dangerouslySetInnerHTML={{
        __html: `
          @media print {
            body { margin: 0; }
            .no-print { display: none !important; }
            .card-container { 
              width: 85.6mm; 
              height: 53.98mm; 
              margin: 0;
              transform: none !important;
              box-shadow: none !important;
            }
          }
          
          @media (max-width: 640px) {
            .card-container {
              width: 320px !important;
              height: 500px !important;
            }
          }
        `
      }} />
    </div>
  );
};

export default PlayerCard;