import React from 'react';
import {GraduationCap } from 'lucide-react'
import { COLOR_YELLOW } from './dynamic-theme';

export const GraduationCapColored: React.FC<any> = (props) => {
  return (
    <GraduationCap style={{
      color: COLOR_YELLOW
    }} {...props} />
  );
};