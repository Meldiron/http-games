import { type InferPageType, loader } from "fumadocs-core/source";
import { lucideIconsPlugin } from "fumadocs-core/source/lucide-icons";
import { icons } from "lucide-react";
import { createElement } from "react";
import { docs } from "@/.source";
import { GraduationCapColored } from "@/components/graduation-cap-colored";
import { Grid3x3Colored } from "@/components/grid-3x3-colored";

// See https://fumadocs.dev/docs/headless/source-api for more info
export const source = loader({
  baseUrl: "/docs",
  source: docs.toFumadocsSource(),
  plugins: [lucideIconsPlugin()],
  icon(icon) {
    if (!icon) return;

    // Handle custom icons
    if (icon === "GraduationCapColored") {
      return createElement(GraduationCapColored);
    } else if (icon === "Grid3x3Colored") {
      return createElement(Grid3x3Colored);
    }

    // Handle standard lucide icons
    if (icon in icons) {
      return createElement(icons[icon as keyof typeof icons]);
    }
  },
});

export function getPageImage(page: InferPageType<typeof source>) {
  const segments = [...page.slugs, "image.png"];

  return {
    segments,
    url: `/og/docs/${segments.join("/")}`,
  };
}

export async function getLLMText(page: InferPageType<typeof source>) {
  const processed = await page.data.getText("processed");

  return `# ${page.data.title}

${processed}`;
}
