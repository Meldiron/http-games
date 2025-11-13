"use client";

import { useEffect, useState } from "react";

interface AnimatedGridProps {
  size?: number;
  className?: string;
}

export function AnimatedGrid({ size = 4, className = "" }: AnimatedGridProps) {
  const [activeTraps, setActiveTraps] = useState<Set<number>>(new Set());
  const [playerPosition, setPlayerPosition] = useState(0);

  // Predefined trap positions for demo
  const trapPositions = new Set([5, 9, 11]);
  const exitPosition = size * size - 1;

  useEffect(() => {
    const interval = setInterval(() => {
      // Simulate player movement
      setPlayerPosition((prev) => {
        const next = (prev + 1) % (size * size);

        // If player hits a trap, show it briefly
        if (trapPositions.has(next)) {
          setActiveTraps(new Set([next]));
          setTimeout(() => {
            setActiveTraps(new Set());
          }, 1000);
          return 0; // Reset to start
        }

        return next;
      });
    }, 1500);

    return () => clearInterval(interval);
  }, [size, trapPositions]);

  const getCellStyle = (index: number) => {
    if (index === playerPosition) {
      return "bg-blue-500 animate-pulse scale-110 shadow-lg";
    }
    if (index === 0) {
      return "bg-green-500"; // Start position
    }
    if (index === exitPosition) {
      return "bg-red-500"; // Exit position
    }
    if (activeTraps.has(index)) {
      return "bg-orange-500 animate-bounce";
    }
    return "bg-gray-300 dark:bg-gray-600 hover:bg-gray-400 dark:hover:bg-gray-500";
  };

  return (
    <div
      className={`grid gap-1 ${className}`}
      style={{ gridTemplateColumns: `repeat(${size}, minmax(0, 1fr))` }}
    >
      {Array.from({ length: size * size }, (_, i) => (
        <div
          key={`cell-${i}-${size}`}
          className={`
            aspect-square rounded-sm transition-all duration-300 ease-in-out
            ${getCellStyle(i)}
          `}
        />
      ))}
    </div>
  );
}
