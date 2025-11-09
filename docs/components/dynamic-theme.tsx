'use client';

import { usePathname } from 'next/navigation';
import { useEffect } from 'react';

export const COLOR_YELLOW = 'oklch(79.5% 0.184 86.047)';
export const COLOR_BLUE = 'oklch(62.7% 0.194 250.214)';

export function DynamicTheme() {
  const pathname = usePathname();

  useEffect(() => {
    const root = document.documentElement;
    
    if (pathname.startsWith('/docs/basics')) {
      // Yellow color (Tailwind yellow-500)
      root.style.setProperty('--color-fd-primary', COLOR_YELLOW);
    } else if (pathname.startsWith('/docs/grid-trap')) {
      // Blue color
      root.style.setProperty('--color-fd-primary', COLOR_BLUE);
    } else {
      // Remove custom property to fall back to default
      root.style.removeProperty('--color-fd-primary');
    }
  }, [pathname]);

  return null;
}