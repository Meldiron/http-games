import { Grid3x3 } from "lucide-react";
import type React from "react";
import { COLOR_BLUE } from "./dynamic-theme";

export const Grid3x3Colored: React.FC<React.ComponentProps<"svg">> = (
  props,
) => {
  return (
    <Grid3x3
      style={{
        color: COLOR_BLUE,
      }}
      {...props}
    />
  );
};
