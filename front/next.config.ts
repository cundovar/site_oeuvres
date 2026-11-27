import type { NextConfig } from "next";

const nextConfig: NextConfig = {
  cacheComponents: true,
  partialPrefetching: true,
  async rewrites() {
    const origine = process.env.BACK_ORIGIN ?? "http://localhost:18080";
    return [
      {
        source: "/api/admin/:path*",
        destination: `${origine}/api/admin/:path*`,
      },
    ];
  },
};

export default nextConfig;
