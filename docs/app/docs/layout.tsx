import { DocsLayout } from "fumadocs-ui/layouts/docs";
import { DynamicTheme } from "@/components/dynamic-theme";
import { baseOptions } from "@/lib/layout.shared";
import { source } from "@/lib/source";

export default function Layout({ children }: LayoutProps<"/docs">) {
  return (
    <DocsLayout tree={source.pageTree} {...baseOptions()}>
      <DynamicTheme />
      {children}
    </DocsLayout>
  );
}
