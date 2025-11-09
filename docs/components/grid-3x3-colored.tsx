import React from 'react';
import {Grid3x3 } from 'lucide-react'
import { COLOR_BLUE } from './dynamic-theme';

export const Grid3x3Colored: React.FC<any> = (props) => {
  return (
    <Grid3x3 style={{
      color: COLOR_BLUE
    }} {...props} />
  );
};