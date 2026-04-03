import type { SVGAttributes } from 'react';

export default function AppLogoIcon(props: SVGAttributes<SVGElement>) {
    return (
        <svg {...props} viewBox="0 0 40 40" xmlns="http://www.w3.org/2000/svg">
            {/* Document/Ticket base */}
            <path
                d="M8 2C6.89543 2 6 2.89543 6 4V36C6 37.1046 6.89543 38 8 38H32C33.1046 38 34 37.1046 34 36V10L26 2H8Z"
                fill="currentColor"
                opacity="0.2"
            />
            <path
                d="M26 2V8C26 9.10457 26.8954 10 28 10H34M8 2C6.89543 2 6 2.89543 6 4V36C6 37.1046 6.89543 38 8 38H32C33.1046 38 34 37.1046 34 36V10M26 2H8M26 2L34 10"
                stroke="currentColor"
                strokeWidth="1.5"
                strokeLinecap="round"
                strokeLinejoin="round"
                fill="none"
            />
            
            {/* AI Neural Network Pattern */}
            {/* Top node */}
            <circle cx="20" cy="14" r="2.5" fill="currentColor" />
            
            {/* Middle left node */}
            <circle cx="13" cy="22" r="2" fill="currentColor" opacity="0.8" />
            
            {/* Middle right node */}
            <circle cx="27" cy="22" r="2" fill="currentColor" opacity="0.8" />
            
            {/* Bottom center node */}
            <circle cx="20" cy="30" r="2.5" fill="currentColor" />
            
            {/* Connecting lines */}
            <line x1="20" y1="16.5" x2="13" y2="20" stroke="currentColor" strokeWidth="1" opacity="0.5" />
            <line x1="20" y1="16.5" x2="27" y2="20" stroke="currentColor" strokeWidth="1" opacity="0.5" />
            <line x1="13" y1="24" x2="20" y2="27.5" stroke="currentColor" strokeWidth="1" opacity="0.5" />
            <line x1="27" y1="24" x2="20" y2="27.5" stroke="currentColor" strokeWidth="1" opacity="0.5" />
            <line x1="15" y1="22" x2="25" y2="22" stroke="currentColor" strokeWidth="1" opacity="0.3" />
            
            {/* AI Sparkle accent */}
            <path
                d="M36 4L35 6L36 8L37 6L36 4Z M36 6H37M36 6H35M36 6V5M36 6V7"
                stroke="currentColor"
                strokeWidth="0.8"
                strokeLinecap="round"
                fill="none"
                opacity="0.7"
            />
        </svg>
    );
}
