import React, { useEffect, useRef, useState } from "react";
import * as THREE from "three";

export function Hero3DCanvas({ className = "" }: { className?: string }) {
  const containerRef = useRef<HTMLDivElement>(null);
  const [isReady, setIsReady] = useState(false);

  useEffect(() => {
    const container = containerRef.current;
    if (!container || typeof window === "undefined") return;

    /* ── SCENE ─────────────────────────────────────────────────────── */
    const scene = new THREE.Scene();
    // NO fog – let the CSS background show cleanly through alpha renderer

    const width = container.clientWidth || window.innerWidth;
    const height = container.clientHeight || window.innerHeight;

    const camera = new THREE.PerspectiveCamera(40, width / height, 0.1, 100);
    camera.position.set(0, 0, 8);

    const renderer = new THREE.WebGLRenderer({
      antialias: true,
      alpha: true, // transparent canvas – background shows through
      powerPreference: "high-performance",
    });
    renderer.setSize(width, height);
    renderer.setPixelRatio(Math.min(window.devicePixelRatio, 2));
    renderer.toneMapping = THREE.ACESFilmicToneMapping;
    renderer.toneMappingExposure = 1.55;
    renderer.shadowMap.enabled = true;
    renderer.shadowMap.type = THREE.PCFShadowMap;

    container.appendChild(renderer.domElement);
    setIsReady(true);

    /* ── LIGHTING ───────────────────────────────────────────────────── */
    // Warm neutral ambient – keeps bag crisp ivory, not green-tinted
    const ambient = new THREE.AmbientLight(0xfff8f0, 1.6);
    scene.add(ambient);

    // Strong warm key from top-right – primary product illumination
    const key = new THREE.DirectionalLight(0xfff8ee, 3.8);
    key.position.set(6, 9, 7);
    key.castShadow = true;
    key.shadow.mapSize.setScalar(1024);
    key.shadow.bias = -0.001;
    scene.add(key);

    // Very subtle cool-green rim from behind-left (brand accent only, minimal green tint)
    const rim = new THREE.DirectionalLight(0xd8ede0, 0.6);
    rim.position.set(-7, -1, -5);
    scene.add(rim);

    // Soft warm bounce under the bag – lifts the product
    const bounce = new THREE.PointLight(0xfff3dc, 1.5, 20);
    bounce.position.set(1.5, -3.5, 4);
    scene.add(bounce);

    /* ── BAG TEXTURE (canvas) ───────────────────────────────────────── */
    const tc = document.createElement("canvas");
    tc.width = 1024;
    tc.height = 1024;
    const ctx = tc.getContext("2d")!;

    // Pure warm ivory base
    ctx.fillStyle = "#f8f3ea";
    ctx.fillRect(0, 0, 1024, 1024);

    // Very subtle grain
    for (let i = 0; i < 28000; i++) {
      const x = Math.random() * 1024;
      const y = Math.random() * 1024;
      ctx.fillStyle = `rgba(60,50,40,${Math.random() * 0.032})`;
      ctx.fillRect(x, y, 1.2, 1.2);
    }

    // Minimalist leaf emblem
    ctx.save();
    ctx.translate(512, 435);
    ctx.beginPath();
    ctx.moveTo(0, -52);
    ctx.bezierCurveTo(46, -30, 46, 30, 0, 52);
    ctx.bezierCurveTo(-46, 30, -46, -30, 0, -52);
    ctx.fillStyle = "rgba(35, 68, 45, 0.88)";
    ctx.fill();
    ctx.beginPath();
    ctx.moveTo(0, -44);
    ctx.lineTo(0, 44);
    ctx.strokeStyle = "#f8f3ea";
    ctx.lineWidth = 2.5;
    ctx.stroke();
    ctx.restore();

    ctx.textAlign = "center";
    ctx.font = "700 30px Manrope, sans-serif";
    ctx.fillStyle = "#1b3826";
    ctx.fillText("EVERGREEN", 512, 530);

    ctx.font = "400 12px Manrope, sans-serif";
    ctx.fillStyle = "rgba(35, 68, 45, 0.65)";
    ctx.fillText("100% COMPOSTABLE BIOPOLYMER", 512, 565);

    ctx.font = "400 10px Manrope, sans-serif";
    ctx.fillStyle = "rgba(35, 68, 45, 0.4)";
    ctx.fillText("EN 13432 • ZERO MICROPLASTICS", 512, 595);

    const bagTexture = new THREE.CanvasTexture(tc);
    bagTexture.needsUpdate = true;

    /* ── BAG GEOMETRY ───────────────────────────────────────────────── */
    const bagGroup = new THREE.Group();
    const BW = 2.1,
      BH = 2.7,
      BD = 0.72;

    const bagGeo = new THREE.BoxGeometry(BW, BH, BD, 48, 64, 24);
    const pos = bagGeo.attributes["position"];
    if (pos) {
      for (let i = 0; i < pos.count; i++) {
        const x = pos.getX(i),
          y = pos.getY(i),
          z = pos.getZ(i);
        const hN = (y + BH / 2) / BH;
        let dz = 0;
        if (Math.abs(x) > BW * 0.45 && hN > 0.1 && hN < 0.9)
          dz -= Math.sin(hN * Math.PI) * 0.08 * Math.cos(z * 4);
        if (hN < 0.25) dz += Math.sin((1 - hN / 0.25) * Math.PI * 0.5) * 0.04;
        const noise =
          Math.sin(x * 6 + y * 5) * 0.014 +
          Math.cos(y * 8 + z * 6) * 0.012 +
          Math.sin((x + z) * 10) * 0.007;
        pos.setXYZ(i, x, y, z + dz + noise);
      }
    }
    bagGeo.computeVertexNormals();

    // Clean ivory physical material – NOT green-tinted
    const bagMat = new THREE.MeshPhysicalMaterial({
      map: bagTexture,
      color: 0xfff8f0, // warm ivory, never green
      roughness: 0.58,
      metalness: 0.0,
      clearcoat: 0.12,
      clearcoatRoughness: 0.45,
      transmission: 0.06,
      thickness: 0.5,
      sheen: 0.35,
      sheenColor: new THREE.Color(0xe8f0e8),
    });

    const bagMesh = new THREE.Mesh(bagGeo, bagMat);
    bagMesh.castShadow = true;
    bagGroup.add(bagMesh);

    /* ── D-CUT HANDLE ────────────────────────────────────────────────── */
    const hShape = new THREE.Shape();
    const hw = 0.45,
      hh = 0.16,
      hr = 0.08;
    hShape.moveTo(-hw + hr, -hh);
    hShape.lineTo(hw - hr, -hh);
    hShape.absarc(hw - hr, 0, hr, -Math.PI / 2, Math.PI / 2, false);
    hShape.lineTo(-hw + hr, hh);
    hShape.absarc(-hw + hr, 0, hr, Math.PI / 2, (3 * Math.PI) / 2, false);
    const hHole = new THREE.Path();
    const iw = hw - 0.04,
      ih = hh - 0.04,
      ir = hr - 0.02;
    hHole.moveTo(-iw + ir, -ih);
    hHole.lineTo(iw - ir, -ih);
    hHole.absarc(iw - ir, 0, ir, -Math.PI / 2, Math.PI / 2, false);
    hHole.lineTo(-iw + ir, ih);
    hHole.absarc(-iw + ir, 0, ir, Math.PI / 2, (3 * Math.PI) / 2, false);
    hShape.holes.push(hHole);
    const hGeo = new THREE.ExtrudeGeometry(hShape, {
      depth: 0.04,
      bevelEnabled: true,
      bevelSegments: 3,
      steps: 1,
      bevelSize: 0.015,
      bevelThickness: 0.015,
    });
    hGeo.center();
    const hMat = new THREE.MeshStandardMaterial({ color: 0x1e3a28, roughness: 0.5 });
    const fHandle = new THREE.Mesh(hGeo, hMat);
    fHandle.position.set(0, BH * 0.36, BD * 0.5 + 0.02);
    bagGroup.add(fHandle);
    const bHandle = fHandle.clone();
    bHandle.position.z = -(BD * 0.5 + 0.02);
    bagGroup.add(bHandle);

    /* ── LOOP HANDLES ────────────────────────────────────────────────── */
    const lc1 = new THREE.CubicBezierCurve3(
      new THREE.Vector3(-0.45, BH * 0.42, BD * 0.35),
      new THREE.Vector3(-0.35, BH * 0.78, BD * 0.38),
      new THREE.Vector3(0.35, BH * 0.78, BD * 0.38),
      new THREE.Vector3(0.45, BH * 0.42, BD * 0.35),
    );
    bagGroup.add(new THREE.Mesh(new THREE.TubeGeometry(lc1, 40, 0.034, 12, false), bagMat));
    const lc2 = new THREE.CubicBezierCurve3(
      new THREE.Vector3(-0.45, BH * 0.42, -BD * 0.35),
      new THREE.Vector3(-0.35, BH * 0.78, -BD * 0.38),
      new THREE.Vector3(0.35, BH * 0.78, -BD * 0.38),
      new THREE.Vector3(0.45, BH * 0.42, -BD * 0.35),
    );
    bagGroup.add(new THREE.Mesh(new THREE.TubeGeometry(lc2, 40, 0.034, 12, false), bagMat));

    bagGroup.position.set(1.5, -0.15, 0.2);
    bagGroup.rotation.set(0.06, -0.28, 0.04);
    scene.add(bagGroup);

    /* ── MINIMAL ORGANIC LEAVES (5 only, very subtle) ────────────────── */
    const leafShape = new THREE.Shape();
    leafShape.moveTo(0, 0);
    leafShape.quadraticCurveTo(0.16, 0.28, 0.07, 0.65);
    leafShape.quadraticCurveTo(0, 0.8, 0, 0.85);
    leafShape.quadraticCurveTo(0, 0.8, -0.07, 0.65);
    leafShape.quadraticCurveTo(-0.16, 0.28, 0, 0);
    const leafGeo = new THREE.ShapeGeometry(leafShape);
    leafGeo.center();

    const leafMats = [
      new THREE.MeshStandardMaterial({
        color: 0x5a8e60,
        roughness: 0.55,
        transparent: true,
        opacity: 0.38,
        side: THREE.DoubleSide,
      }),
      new THREE.MeshStandardMaterial({
        color: 0x3a6344,
        roughness: 0.5,
        transparent: true,
        opacity: 0.3,
        side: THREE.DoubleSide,
      }),
      new THREE.MeshStandardMaterial({
        color: 0x8ab08e,
        roughness: 0.6,
        transparent: true,
        opacity: 0.28,
        side: THREE.DoubleSide,
      }),
    ];

    interface LeafData {
      mesh: THREE.Mesh;
      speedY: number;
      rotX: number;
      rotY: number;
      rotZ: number;
      driftPhase: number;
    }
    const leaves: LeafData[] = [];

    for (let i = 0; i < 5; i++) {
      const mat = leafMats[i % leafMats.length];
      const leaf = new THREE.Mesh(leafGeo, mat);
      const s = 0.22 + Math.random() * 0.28;
      leaf.scale.set(s, s * (1 + Math.random() * 0.25), s);
      leaf.position.set(
        (Math.random() - 0.5) * 14,
        (Math.random() - 0.5) * 9,
        (Math.random() - 0.5) * 5 - 2,
      );
      leaf.rotation.set(
        Math.random() * Math.PI * 2,
        Math.random() * Math.PI * 2,
        Math.random() * Math.PI * 2,
      );
      scene.add(leaf);
      leaves.push({
        mesh: leaf,
        speedY: -(0.002 + Math.random() * 0.0035),
        rotX: (Math.random() - 0.5) * 0.013,
        rotY: (Math.random() - 0.5) * 0.016,
        rotZ: (Math.random() - 0.5) * 0.01,
        driftPhase: Math.random() * Math.PI * 2,
      });
    }

    /* ── MINIMAL DUST PARTICLES (25, very small) ─────────────────────── */
    const pCount = 25;
    const pGeo = new THREE.BufferGeometry();
    const pPos = new Float32Array(pCount * 3);
    for (let i = 0; i < pCount * 3; i += 3) {
      pPos[i] = (Math.random() - 0.5) * 16;
      pPos[i + 1] = (Math.random() - 0.5) * 11;
      pPos[i + 2] = (Math.random() - 0.5) * 8;
    }
    pGeo.setAttribute("position", new THREE.BufferAttribute(pPos, 3));
    const pMat = new THREE.PointsMaterial({
      color: 0xf5ede0,
      size: 0.022,
      transparent: true,
      opacity: 0.28,
    });
    const particles = new THREE.Points(pGeo, pMat);
    scene.add(particles);

    /* ── MOUSE PARALLAX ─────────────────────────────────────────────── */
    let tRotX = 0.06,
      tRotY = -0.28,
      tPosX = 1.5,
      tPosY = -0.15;
    let mX = 0,
      mY = 0;

    const onMouse = (e: MouseEvent) => {
      mX = (e.clientX / window.innerWidth) * 2 - 1;
      mY = -(e.clientY / window.innerHeight) * 2 + 1;
      tRotY = -0.28 + mX * 0.28;
      tRotX = 0.06 - mY * 0.2;
      tPosX = window.innerWidth >= 768 ? 1.5 + mX * 0.32 : 0;
      tPosY = -0.15 + mY * 0.22;
    };
    window.addEventListener("mousemove", onMouse);

    /* ── RESIZE ─────────────────────────────────────────────────────── */
    const onResize = () => {
      if (!container) return;
      const nw = container.clientWidth,
        nh = container.clientHeight;
      camera.aspect = nw / nh;
      camera.updateProjectionMatrix();
      renderer.setSize(nw, nh);
      if (nw < 768) {
        bagGroup.scale.setScalar(0.72);
        bagGroup.position.set(0, -0.5, 0);
        tPosX = 0;
        tPosY = -0.5;
      } else {
        bagGroup.scale.setScalar(1);
        tPosX = 1.5;
        tPosY = -0.15;
      }
    };
    window.addEventListener("resize", onResize);
    onResize();

    /* ── ANIMATION ──────────────────────────────────────────────────── */
    let rafId: number;
    const timer = new THREE.Timer();

    const animate = () => {
      rafId = requestAnimationFrame(animate);
      timer.update();
      const t = timer.getElapsed();

      // Bag – gentle float
      const fY = Math.sin(t * 1.1) * 0.07;
      const fZ = Math.cos(t * 0.85) * 0.018;
      bagGroup.position.x += (tPosX - bagGroup.position.x) * 0.045;
      bagGroup.position.y += (tPosY + fY - bagGroup.position.y) * 0.045;
      bagGroup.rotation.x += (tRotX - bagGroup.rotation.x) * 0.045;
      bagGroup.rotation.y += (tRotY - bagGroup.rotation.y) * 0.045;
      bagGroup.rotation.z += (fZ - bagGroup.rotation.z) * 0.045;

      // Leaves
      leaves.forEach((l) => {
        l.mesh.position.y += l.speedY;
        l.mesh.position.x += Math.sin(t + l.driftPhase) * 0.004;
        l.mesh.rotation.x += l.rotX;
        l.mesh.rotation.y += l.rotY;
        l.mesh.rotation.z += l.rotZ;
        if (l.mesh.position.y < -5.5) {
          l.mesh.position.y = 5.5;
          l.mesh.position.x = (Math.random() - 0.5) * 14;
        }
      });

      // Particles – slow drift
      particles.rotation.y = t * 0.015;
      particles.rotation.x = t * 0.008;

      // Subtle camera micro-motion
      camera.position.x = Math.sin(t * 0.28) * 0.06 + mX * 0.1;
      camera.position.y = Math.cos(t * 0.22) * 0.06 + mY * 0.1;
      camera.lookAt(0, 0, 0);

      renderer.render(scene, camera);
    };
    animate();

    /* ── CLEANUP ─────────────────────────────────────────────────────── */
    return () => {
      window.removeEventListener("mousemove", onMouse);
      window.removeEventListener("resize", onResize);
      cancelAnimationFrame(rafId);
      timer.dispose();
      renderer.dispose();
      bagGeo.dispose();
      bagMat.dispose();
      hGeo.dispose();
      hMat.dispose();
      bagTexture.dispose();
      leafGeo.dispose();
      leafMats.forEach((m) => m.dispose());
      pGeo.dispose();
      pMat.dispose();
      if (renderer.domElement && container.contains(renderer.domElement))
        container.removeChild(renderer.domElement);
    };
  }, []);

  return (
    <div
      ref={containerRef}
      className={`absolute inset-0 z-10 pointer-events-none overflow-hidden ${className}`}
      style={{ opacity: isReady ? 1 : 0, transition: "opacity 1.4s ease" }}
    />
  );
}
