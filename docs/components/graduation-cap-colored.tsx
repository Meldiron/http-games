import { GraduationCap } from "lucide-react";
import type React from "react";
import { COLOR_YELLOW } from "./dynamic-theme";

export const GraduationCapColored: React.FC<React.ComponentProps<"svg">> = (
  props,
) => {
  return (
    <GraduationCap
      style={{
        color: COLOR_YELLOW,
      }}
      {...props}
    />
  );
};
